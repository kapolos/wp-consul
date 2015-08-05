# wp-consul
Storing data in Consul's Key/Value store via WordPress

[Consul](https://www.consul.io) is a distributed KV store, used mainly for service/configuration discovery, service monitoring and general automation facilitation. 

WP Consul is a Wordpress plugin that saves arbitrary data from WordPress into Consul's KV store. It updates the data hourly (via WP Cron functionality)

### How to use

#### Create a key in Consul
For example:

![](https://github.com/kapolos/wp-consul/raw/gh-pages/images/consul%20-%20create%20key.png)

#### Configure ACL
We allow the plugin to create arbitrary keys under the key we created:

![](https://github.com/kapolos/wp-consul/raw/gh-pages/images/consul%20-%20create%20acl.png)

Notice that in this example I have a rule that allows read access to the whole store. This is probably **not** what you want to do, so I am pointing this out explicitly to have in mind.

#### Fill the plugin settings
Example:

![](https://github.com/kapolos/wp-consul/raw/gh-pages/images/wpconsul%20-%20settings.png)

### How to define what data you want to WP Consul to store

Inside the function `wpconsul_updateStore()`, there is a closure that defines the payload for the key. Simply modify it to meet your use case.

```php
    /**
     * Modify this closure for your use case
     *
     * @type callable $payload
     * @return string
     */
    $payload = function () {
        return 'Change Me';
    };
````
