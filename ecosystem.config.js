module.exports = {
  apps: [
    {
      name: "ecommerce_app",
      script: "/usr/bin/php",
      args: "-S 0.0.0.0:8000 -t .",
      interpreter: "none",
      watch: false,
      instances: 1,
      exec_mode: "fork",
      env: {
        APP_ENV: "development"
      },
      env_production: {
        APP_ENV: "production"
      }
    }
  ]
};