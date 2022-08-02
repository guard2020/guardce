var mix = require("laravel-mix");

mix
  .sass("resources/sass/app.scss", "public/css")
  .styles("resources/css/custom.css", "public/css/custom.css")
  .styles("resources/assets/css/custom.css", "public/css/smartdashboard.css")
  // Webpack configuration for live reload, but requires a php artisan serve in port 8000
  .browserSync("localhost:8000")
  .disableNotifications()
  .options({
    hmrOptions: {
      host: "localhost",
      port: 3000,
    },
  });

if (process.env.NODE_ENV === "production") {
  // Unused code...
  var imagesPath = "resources/limitless/global_assets/images",
    themeCss = "resources/limitless/material/css",
    themeAppJs = "resources/limitless/layouts/layout_2/material/js",
    iconsPath = "resources/limitless/global_assets/css/icons/icomoon",
    materialIconsPath = "resources/limitless/global_assets/css/icons/material",
    jsPath = "resources/limitless/global_assets/js/main",
    jsPluginPath = "resources/limitless/global_assets/js/plugins";
  // Other resources appart from Limitless
  mix
    .copy("resources/images", "public/images")
    .copy("resources/assets/js", "public/js")
    .copy("resources/plugins", "public/plugins")

    // We don't need to rebuild these, since we already have these assets in public dir.
    // Limitless Theme Resources
    .copy(imagesPath, "public/limitless/images")
    .copy(iconsPath, "public/limitless/css/icons/icomoon")
    .copy(materialIconsPath, "public/limitless/css/icons/material")
    .copy(themeCss, "public/limitless/material/css")
    .copy(themeAppJs, "public/limitless/material/js")
    .copy(jsPath, "public/limitless/js")
    .copy(jsPluginPath, "public/limitless/js/plugins");
}
