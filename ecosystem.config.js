module.exports = {
  apps: [
    {
      name: "ecommerce-app",
      script: "php",
      args: "-S 0.0.0.0:8000 -t .",
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
