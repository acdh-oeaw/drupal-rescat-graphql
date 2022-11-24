## drupal resource catalog
The GraphQL Query plugin for the Resource Catalog.

## API Documentation:

https://www.postman.com/nczirjak-acdh/workspace/resource-catalog/overview

Click on the three dots next to the Collections and Select the "View Documentation" option to see the Documentation.


## Permissions:

We have the OAuth plugin, which will cover the GRAPHQL permissions. First you have to get a Bearer Token from the OAuth API endpoint:

- url: https://rescat.acdh-dev.oeaw.ac.at/oauth/token

Params:
- username (Your Drupal username)
- password (Your Drupal password)
- client_secret : (your_secret)
- grant_type : password
- client_id : 54388f4f-591e-470d-9360-8cf0bc2b897b
- scope: content_editor

In the response, you will get an access_token string, you have to pass it as Bearer Token to our graphQL endpoint: https://rescat.acdh-dev.oeaw.ac.at/graphql

