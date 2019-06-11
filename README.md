# learning-php-slim-vue

A simple PHP tutorial project which contains a PHP API server built using the Slim3 framework skeleton and a complete Vue.js app communicating with said server.

This project uses Docker to setup its development environment, and the Vue UI enables you to build Docker images using the `docker.sock` bind trick (I will add more on this later on, in the meantime please look in the `docker-compose.yml` file for more details).  
It even allows you to enter your AWS profile details (which are saved to a local SQLite database) and use them to perform several AWS ECS related tasks.

I appreciate any feedback on this demo project, and would love to hear any opinions on how to improve this short learning project.

Thanks!
