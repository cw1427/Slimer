# Slimer flash message

Slimer has a very convinent flash message feature based on **slim/flash** and **kanellov/slim-twig-flash** to render the flash message in the page.


## session based flash message

Slimer register a flash service in the container

```PHP
        $container['flash'] = function (Container $container) {
            return new \Slim\Flash\Messages();
        };

$this->flash->addMessage('success','username or password is not correct');
$this->flash->addMessage('warning','username or password is not correct');
$this->flash->addMessage('danger','username or password is not correct');
return $this->response->withRedirect($this->router->pathFor('index'));


```

## flash show message for the current request

Slimer flash as default use $this->flash->addMessage(....), so it will been rendered in the next coming request.

So it is common to use it when do the redirect. 

> If want ot render the message in the current request use below

**$this->flash->addMessageNow(....)**

## flash message rendering 

Slimer as default, define the **danger** message permanent, 1 min fadeout for **warning**, 30 seconds for **success**

```HTML
{% import _self as this %}

{% if flash()|length %}
<div class="" style="width:500px;margin-right:auto;margin-left:auto">
<div class="flyover">
{% for class, messages in flash() %}
    {% for msg in messages %}
    <div id="flash_{{ class }}_{{ loop.index }}" class="alert alert-{{ class }} flash-alert alert-dismissible" role="alert">
    	<h4><i class="icon fa fa-ban"></i> Alert!</h4>
        {{ msg|raw }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <script>
    	$(function () {
    		{% if class == "danger" %}
    		 $("#flash_{{ class }}_{{ loop.index }}").fadeIn(1000);
    		{% elseif class == "warning" %}
    		$("#flash_{{ class }}_{{ loop.index }}").fadeIn(1000).delay(2000).fadeOut(60000);
    		{% else %}
    		$("#flash_{{ class }}_{{ loop.index }}").fadeIn(1000).delay(2000).fadeOut(30000);
    		{% endif %}
        });
    </script>
    {% endfor %}
{% endfor %}
</div>
</div>
{% endif %}

```