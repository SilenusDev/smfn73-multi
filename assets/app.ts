import './bootstrap';
/*
 * Welcome to your app's main TypeScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Import Lucide icons
import { 
    createIcons, 
    CheckCircle, 
    AlertCircle, 
    Zap,
    LogIn,
    LogOut,
    UserPlus,
    Building2,
    Shield,
    Mail,
    Moon,
    Flame,
    UserCircle,
    Rocket,
    Sparkles,
    Lock,
    Database,
    Palette,
    Layers,
    Target,
    Menu,
    Home,
    Info,
    User
} from 'lucide';

// Initialize Lucide icons on page load
document.addEventListener('DOMContentLoaded', (): void => {
    createIcons({
        icons: {
            CheckCircle,
            AlertCircle,
            Zap,
            LogIn,
            LogOut,
            UserPlus,
            Building2,
            Shield,
            Mail,
            Moon,
            Flame,
            UserCircle,
            Rocket,
            Sparkles,
            Lock,
            Database,
            Palette,
            Layers,
            Target,
            Menu,
            Home,
            Info,
            User
        }
    });
});

console.log('This log comes from assets/app.ts - welcome to TypeScript! 🎉');
