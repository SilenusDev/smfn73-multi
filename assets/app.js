import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
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
    Target
} from 'lucide';

// Initialize Lucide icons on page load
document.addEventListener('DOMContentLoaded', () => {
    createIcons({
        icons: {
            CheckCircle,
            AlertCircle,
            Zap,
            LogIn,
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
            Target
        }
    });
});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
