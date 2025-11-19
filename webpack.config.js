
// Import de Symfony Webpack Encore
import Encore from '@symfony/webpack-encore';

// Vérifie si l'environnement de runtime est configuré (dev ou prod)
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

// Configuration Webpack Encore
Encore
    // Dossier où les fichiers compilés seront générés
    .setOutputPath('public/build/')
    
    // Chemin public pour accéder aux fichiers compilés depuis le navigateur
    .setPublicPath('/build')
    
    // Entry JS principal : ton fichier app.js contient tous tes imports JS
    .addEntry('app', './assets/app.js')
    
    // Entry CSS principal : ton fichier app.css contient tous tes styles
    .addStyleEntry('styles', './assets/styles/app.css')
    
    // Crée un fichier runtime séparé pour optimiser le cache
    .enableSingleRuntimeChunk()
    
    // Supprime automatiquement le contenu du dossier build avant chaque build
    .cleanupOutputBeforeBuild()
    
    // Notifications de build sur le bureau
    .enableBuildNotifications()
    
    // Source maps utiles en dev pour le débogage
    .enableSourceMaps(!Encore.isProduction())
    
    // Versioning des fichiers (hash) pour éviter le cache en prod
    .enableVersioning(Encore.isProduction())
    
;

// Exporte la configuration Webpack finale
export default Encore.getWebpackConfig();

