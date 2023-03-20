<?php
/*
Plugin Name: ACF Shopify
Description: Extends ACF to fetch Shopify products for custom fields.
Author: https://github.com/mikepawlinski
Version: 0.0.3
*/

require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';

function acfs_get_products()
{
  $client = new \GuzzleHttp\Client([
    'base_uri' => 'https://blueprintbetatest.myshopify.com/api/2023-01/graphql.json',
    'headers' => [
      'Content-Type' => 'application/json',
      'X-Shopify-Storefront-Access-Token' => '3accc15150c6fadc731a4763deb6a2ee'
    ]
  ]);

  $query = <<<EOD
  query {
    products(first: 250) {
      edges {
        node {
          id
          title
          variants(first: 250) {
            nodes {
              sku
            }
          }
        }
      }
    }
  }
  EOD;

  $response = $client->post('', [
    'body' => json_encode(['query' => $query])
  ]);
  $result = json_decode($response->getBody()->getContents());

  return $result->data->products;
}

function acfs_load_custom_field($field)
{
  $products = acfs_get_products();

  $choices = [];

  foreach ($products->edges as $edge) {
    $product = $edge->node;

    $choices[$product->id] = $product->title;
  }

  $field['choices'] = $choices;

  return $field;
}

add_filter('acf/load_field/name=shopify_product', 'acfs_load_custom_field');
