import { BookOpen, DollarSign, Heart, HelpCircle, Mail, Play, Shield, Users, Zap } from 'lucide-vue-next';
import { ref } from 'vue';

// Expanded testimonials data
export const expandedTestimonials = ref([
    {
        id: 1,
        name: 'María González',
        role: 'Diabetic patient',
        location: 'Madrid, Spain',
        content:
            'ReMedi has helped me maintain perfect control of my medication. The PDF reports I generate for my doctor have significantly improved our appointments.',
        rating: 5,
    },
    {
        id: 2,
        name: 'Dr. Carlos Ruiz',
        role: 'Internist',
        location: 'Barcelona, Spain',
        content:
            'I recommend ReMedi to my patients. The detailed adherence reports allow me to track more effectively and better personalize treatments.',
        rating: 5,
    },
    {
        id: 3,
        name: 'Ana Martín',
        role: 'Family caregiver',
        location: 'Valencia, Spain',
        content:
            "The secure sharing feature is incredible. I can monitor my mother's medication remotely and receive alerts when she needs to restock her medicines.",
        rating: 5,
    },
    {
        id: 4,
        name: 'José Luis Pérez',
        role: 'Cardiac patient',
        location: 'Seville, Spain',
        content:
            'Since I use ReMedi, I haven’t missed a single dose. The interface is very intuitive and the reminders fit perfectly into my daily routine.',
        rating: 5,
    },
    {
        id: 5,
        name: 'Dra. Carmen López',
        role: 'Geriatrician',
        location: 'Bilbao, Spain',
        content:
            'ReMedi has revolutionized how my older patients manage their medications. The caregivers feature is especially valuable for families.',
        rating: 5,
    },
    {
        id: 6,
        name: 'Roberto Silva',
        role: 'Hypertension patient',
        location: 'Zaragoza, Spain',
        content: 'The adherence reports have helped me understand my medication patterns. My doctor is very happy with my progress.',
        rating: 5,
    },
    {
        id: 7,
        name: 'Elena Rodríguez',
        role: 'Nurse',
        location: 'Málaga, Spain',
        content: 'As a healthcare professional, I appreciate ReMedi’s accuracy and ease of use. I constantly recommend it to my patients.',
        rating: 5,
    },
    {
        id: 8,
        name: 'Miguel Ángel Torres',
        role: 'Arthritis patient',
        location: 'Murcia, Spain',
        content: 'The inventory management is fantastic. I’ve never run out of medications again thanks to ReMedi’s smart alerts.',
        rating: 5,
    },
    {
        id: 9,
        name: 'Lucía Fernández',
        role: 'Professional caregiver',
        location: 'Las Palmas, Spain',
        content: 'I manage the medication of several patients with ReMedi. The platform is robust, secure, and very easy to use for professionals.',
        rating: 5,
    },
    {
        id: 10,
        name: 'Dr. Antonio Jiménez',
        role: 'Cardiologist',
        location: 'Córdoba, Spain',
        content: 'The adherence data provided by ReMedi is invaluable for adjusting treatments. It’s an essential tool in my practice.',
        rating: 5,
    },
    {
        id: 11,
        name: 'Isabel Moreno',
        role: 'Type 2 diabetes patient',
        location: 'Valladolid, Spain',
        content: 'ReMedi has given me the confidence to manage my diabetes independently. The reminders never fail and the app is very intuitive.',
        rating: 5,
    },
    {
        id: 12,
        name: 'Francisco Ruiz',
        role: 'Patient on multiple medications',
        location: 'Santander, Spain',
        content:
            'With 8 different medications, ReMedi has been my lifesaver. It organizes everything perfectly and I never get confused with the doses.',
        rating: 5,
    },
]);

// Stats data
export const stats = ref([
    { value: '50K+', label: 'Active Users' },
    { value: '2M+', label: 'Doses Remembered' },
    { value: '95%', label: 'Average Adherence' },
    { value: '24/7', label: 'Support Available' },
]);

// Enhanced Features data with detailed bullet points
export const enhancedFeatures = ref([
    {
        illustrationType: 'pills' as const,
        title: 'Medication Registration',
        description: 'Easily record all your medications and prescriptions in one secure place.',
        details: ['Barcode scanning for quick entry', 'Comprehensive medication database', 'Detailed dosage and frequency information'],
    },
    {
        illustrationType: 'calendar' as const,
        title: 'Flexible Schedules',
        description: 'Personalize your medication schedule to fit your unique lifestyle.',
        details: ['Custom schedules per medication', 'Complex interval settings', 'Automatic adaptation to time zone changes'],
    },
    {
        illustrationType: 'bell' as const,
        title: 'Smart Reminders',
        description: 'Receive timely and intelligent reminders so you never miss a dose.',
        details: ['Customizable push notifications', 'Escalated reminders for missed doses', 'Integration with voice assistants'],
    },
    {
        illustrationType: 'dashboard' as const,
        title: 'Dynamic Dashboard',
        description: 'Visualize your health journey with a dynamic dashboard that highlights key stats.',
        details: ['Overview of upcoming doses', 'Real-time adherence metrics', 'Customizable widgets to fit your needs'],
    },
    {
        illustrationType: 'chart' as const,
        title: 'Adherence Tracking',
        description: 'Monitor your medication adherence with detailed reports and analytics.',
        details: ['Daily, weekly, and monthly adherence percentages', 'Dose-level tracking for each medication', 'Printable, shareable PDF reports'],
    },
    {
        illustrationType: 'users' as const,
        title: 'Caregiver Access',
        description: 'Securely share your information with family members or caregivers.',
        details: ['Invite caregivers via secure link', 'Set granular permissions for each person', 'Revoke access at any time'],
    },
]);

// FAQ data
export const faqs = ref([
    {
        question: 'Is ReMedi free?',
        answer: 'Yes, ReMedi offers a complete free plan. We also have Pro and Professional plans with advanced features for users who need more.',
    },
    {
        question: 'Is my health data secure?',
        answer: 'Security is our top priority. We use end-to-end encryption and comply with international medical data protection standards, including HIPAA.',
    },
    {
        question: 'Can I use ReMedi to care for a family member?',
        answer: 'Yes! The Share & Care feature is designed exactly for that. You can invite other users to help you monitor a treatment securely.',
    },
    {
        question: 'How does adherence tracking work?',
        answer: 'ReMedi automatically records when you confirm each dose and generates visual adherence reports you can share with your doctor.',
    },
    {
        question: 'Can I generate reports for my doctor?',
        answer: 'Absolutely. ReMedi generates detailed PDF reports with your adherence statistics, medication patterns, and progress that you can print or share directly with your care team.',
    },
    {
        question: 'How does inventory management work?',
        answer: 'ReMedi automatically tracks your remaining doses and sends customizable alerts when you need to restock. You can log new prescriptions with a single click.',
    },
    {
        question: 'Can I cancel my subscription at any time?',
        answer: 'Yes, you can cancel your subscription at any time from your dashboard. There are no long-term commitments or cancellation penalties.',
    },
    {
        question: 'What’s the difference between the plans?',
        answer: 'The Basic plan is free with essential features. The Pro plan includes advanced features like detailed reports and more caregivers. The Professional plan is designed for healthcare professionals managing multiple patients.',
    },
]);

// Desktop navigation links
export const productLinks = ref([
    {
        key: 'main-features',
        label: 'Main Features',
        links: [
            {
                href: '#how-to-start',
                label: 'How to Start',
                icon: Play,
                section: 'how-to-start',
                description: 'Three Simple Steps to Start',
            },
            {
                href: '#features',
                label: 'Features',
                icon: Zap,
                section: 'features',
                description: 'Enhanced Features',
                // description: 'Reminders, Schedules, and Basic Tracking',
            },
            {
                href: '#benefits',
                label: 'Benefits',
                icon: Heart,
                section: 'benefits',
                description: 'Why Choose ReMedi',
                // description: 'Dashboard Intuitive and Total Control',
            },
        ],
    },
    {
        key: 'advanced-features',
        label: 'Advanced Features',
        links: [
            {
                href: '#advanced-features',
                label: 'Pro Features',
                icon: Shield,
                section: 'advanced-features',
                description: 'Advanced Features for Professionals',
                // description: 'Advanced Features',
                // description: 'Reports, Share Securely, and Inventory',
            },
        ],
    },
]);

// mobile navigation links
export const productLinksMobile = ref([
    { href: '#how-to-start', label: 'How to Start', icon: Play, section: 'how-to-start' },
    { href: '#features', label: 'Features', icon: Zap, section: 'features' },
    { href: '#benefits', label: 'Benefits', icon: Heart, section: 'benefits' },
    { href: '#advanced-features', label: 'Advanced Features', icon: Shield, section: 'advanced-features' },
    // { href: '#demo', label: 'Demo', icon: Play, section: 'demo' },
    { href: '#pricing', label: 'Pricing', icon: DollarSign, section: 'pricing' },
]);

// Desktop resource links
export const resourceLinks = ref([
    {
        href: '#faq',
        label: 'FAQ',
        icon: HelpCircle,
        section: 'faq',
        description: 'Frequently Asked Questions',
        // description: 'Answer to Common Questions',
    },
    {
        href: '#testimonials',
        label: 'Testimonials',
        icon: Users,
        section: 'testimonials',
        description: 'Real Stories from Our Users',
    },
    {
        href: '#help',
        label: 'Help Center',
        icon: BookOpen,
        section: 'help',
        // description: 'Help Center',
        // description: 'Guides and Support',
        description: 'Guides and Complete Documentation',
    },
]);

// Mobile resource links
export const resourceLinksMobile = ref([
    {
        href: '#faq',
        label: 'FAQ',
        icon: HelpCircle,
        section: 'faq',
        description: 'Frequently Asked Questions',
        // description: 'Answer to Common Questions',
    },
    {
        href: '#testimonials',
        label: 'Testimonials',
        icon: Users,
        section: 'testimonials',
        description: 'Real Stories from Our Users',
    },
    {
        href: '#help',
        label: 'Help Center',
        icon: BookOpen,
        section: 'help',
        // description: 'Help Center',
        // description: 'Guides and Support',
        description: 'Guides and Complete Documentation',
    },
    {
        href: '#contact',
        label: 'Contact',
        icon: Mail,
        section: 'contact',
        description: 'Contact Us',
    },
]);

// Benefits data for the landing page (used by Benefits.vue)
export const benefits = ref([
    {
        key: 'dashboard',
        illustration: 'DashboardIllustration',
        title: 'Full control at a glance',
        description:
            'Our dashboard provides a complete view of your treatment. See your next dose, adherence progress, and key statistics — all organized intuitively.',
    },
    {
        key: 'registration',
        illustration: 'AddMedicationIllustration',
        title: 'Register medications easily',
        description:
            'Adding new medications is simple and intuitive. Configure complex schedules, set personalized reminders, and keep all your information organized.',
    },
    {
        key: 'history',
        illustration: 'HistoryIllustration',
        title: 'Monitor your progress',
        description:
            'Keep a detailed history of every dose. Visualize patterns, identify improvements, and share reports with your doctor for better follow-up.',
    },
]);
