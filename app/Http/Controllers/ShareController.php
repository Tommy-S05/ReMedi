<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreShareRequest;
use App\Models\ResourceShare;
use App\Services\ShareableTypeRegistry;
use App\Services\SharingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use InvalidArgumentException;

final class ShareController extends Controller
{
    public function __construct(private readonly SharingService $sharingService) {}

    /**
     * Store a new share invitation.
     *
     * @param StoreShareRequest $request
     * @return RedirectResponse
     */
    public function store(StoreShareRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $modelClass = ShareableTypeRegistry::getModelClass($validated['shareable_type']);

        $resource = $modelClass::findOrFail($validated['shareable_id']);

        Gate::authorize('share', $resource);

        try {
            $this->sharingService->createInvitation(Auth::user(), $resource, $validated['email']);

            return back();
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['email' => __($e->getMessage())]);
        }
    }

    /**
     * Accept a share invitation from the email link.
     */
    public function accept(Request $request, string $token)
    {
        if (!Auth::check()) {
            // Guardar la URL de intención y redirigir al login
            return redirect()->guest(route('login'))
                ->with('intended', $request->fullUrl());
        }

        try {
            $share = $this->sharingService->acceptInvitation($token, Auth::user());

            // return redirect()->route('dashboard')
            //     ->with('success', __('Invitation accepted successfully!'));
        } catch (InvalidArgumentException $e) {
            return Inertia::render('shares/InvalidShare', [
                'message' => $e->getMessage(),
            ])
                ->toResponse($request)
                ->setStatusCode(400);
        }

        $resourceType = ShareableTypeRegistry::getShareableType($share->shareable_type);
        $redirectRoute = "{$resourceType}s.show";

        return redirect()->route($redirectRoute, $share->shareable_id)
            ->with('success', 'You now have access to the shared resource.');
    }

    // public function revoke(ResourceShare $share)
    // {
    //     try {
    //         $this->sharingService->revokeShare($share, Auth::user());

    //         return back()->with('success', 'Share revoked successfully.');
    //     } catch (InvalidArgumentException $e) {
    //         return back()->with('error', $e->getMessage());
    //     }
    // }

    // public function myShares()
    // {
    //     $sharedResources = $this->sharingService->getSharedResources(Auth::user());

    //     return inertia('Shares/Index', compact('sharedResources'));
    // }
}
