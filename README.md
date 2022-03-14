# PHP Node
Simple library for executing short-lived NodeJS processes from PHP.

Hi Packagist :) https://packagist.com/orgs/lifespikes/packages/3021975

### Who is this for?
Every language and platform has their pros and cons, for us one of the biggest benefits of
JavaScript is being able to whip up programs in the span of a few minutes.

This package is for developers who want a simple, object-oriented, library to integrate
microservices written in Node in their PHP apps.

### All this for a process manager?
PHP Node is a library we built to leverage power from multiple languages different members
of our team specialize in and their awesome ecosystems. Naturally we want these integrations
to be as seamless as possible with the rest of our code, but we also don't want to make
anything more complicated than it needs to be.

Instead of implementing separate APIs for each service using let's say a SOAP or REST
interface using Express, or deploying these services as docker containers, PHP Node helps
us:

- Execute scripts using a simple one-line syntax
- Mitigate disconnects in error reporting
- Quickly deploy new microservices with minimal setup
- Test integrations by asserting `FinishedProcess` properties
- Send and receive information without using arguments
- Easily determine error reasons, exit status, and other details

### Examples
Using PHP Node is pretty straight forward.

The very first step is going to be making sure you have **NodeJS** installed on your
machine. The version you run is up to you. Then, make sure the path to Node is present
in your `PATH`, or set as a `NODE_BINARY` environment variable.

Now make sure your script is accessible by your web server or PHP process owner.

Lastly, make sure to expect input on your script by receiving it using `stdin`, instead
of `argv`. We opted for using the stream route to allow us to broadcast reliable and larger
chunks of data.

(_This library is pretty awesome at this: https://www.npmjs.com/package/get-stdin_)

Then, just run it. In this example we're using a microservice to take a screenshot of a
website:

```php
<?php

namespace LifeSpikes\PHPNode\Engine;

use \scripts_path;

/* You can add arguments to the script if needed */
$script = Engine::spawn(scripts_path('screenshot.js'));

/* Use "with" to send data before executing */
$result = $script->with([
    'url'   =>  'https://google.com'
]);

printf(
    "Job ID: %s, Exit Code: %d", 
    $result->output['job_id'], 
    $result->status()
);

/* If not sending any data, you can use ->run() */
```

For context, we'll write another small pseudo-script:

```typescript
import screenshot from 'web-browser-lib';
import getStdin from 'get-stdin';
import {stdout} from 'process';

const stdout = (json) => stdout.write(
  JSON.stringify(json)
);

(async () => {
  const input = JSON.parse(await getStdin());
  const job = await screenshot(input.url);
  
  /* console.log would have the same result */
  stdout({
    job_id: job.id
  });
})();
```
