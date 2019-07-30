# Slimer Template

Slimer has [AdminLTE](https://adminlte.io/themes/AdminLTE/index2.html) builtin. 

Please refer app/Templates/adminlte/* to see the AdminLTE templates.

## Twig template engine

> Slimer template based on Twig

[Twig](https://twig.symfony.com/doc/2.x/)

> HTML config

```PHP
<?php
/**
 * Author: Shawn Chen
 * Desc: The HTML template engine config
 */
return [
    'template_path' => APP_PATH . DS . 'Templates',
    'cache_path' =>  APP_PATH . DS . 'Cache' ,
    'auto_reload' => ('prod' === \getenv('APP_ENV')) ? false : true
];

```

Slimer as default defination in the app/Templates/*

## Slimer view

> Slimer register a view service in the container to initialize the Slimer view layer

Slimer provides several Twig extension in the view.

```PHP
        $container['view'] = function (Container $container) {
            $settings = $container['config']('html', []);
            $view = new \Slim\Views\Twig($settings['template_path'], [
                'cache' => $settings['cache_path'],
                'auto_reload' => $settings['cache_path']
            ]);
            
            // Instantiate and add Slim specific extension
            $basePath = \rtrim(\str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
            $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));
            $view->addExtension(new \Knlv\Slim\Views\TwigMessages($container['flash']));
            $view->addExtension(new \Slimer\Html\CsrfExtension($container['csrf_middleware']));
            $view->addExtension(new \nochso\HtmlCompressTwig\Extension());
            
            $view->addExtension(new \Slimer\Html\SideBarExtension($container));
            $view->addExtension(new \Slimer\Html\NavBarExtension($container));
            return $view;
        };
```

## Render the view

> Slimer use below function in the generic Controller class to render the template.

```PHP
   /**
     * Render view.
     *
     * @param string $template    Template file name
     * @param array  $vars        Template vars
     * @param int    $status_code HTTP status code, default: 200
     *
     * @return ResponseInterface
     */
    public function render( $template, array $vars = [],  $status_code = 200)
    {
        $vars['container'] = $this->container;
        return $this->view->render($this->response, $template, $vars)->withStatus($status_code);
    } 
```

> Use it in the Controller class

```PHP
class Samples extends \Slimer\Controller
{
    
    public function formAction(){
        //$this->logger->debug('test add debug message');
        //$this->flash->addMessageNow('success','success');
        //$this->flash->addMessage('warning','warning in next request');
        return $this->render('samples/form.html.twig',['al'=>['key1'=>'value1','key2'=>'value2']]);
    }
    .......

```

Ablove sample means, this action want to render the template under app/Templates/samples/form.html.twig,  and the parameter is the data for this Twig template.


## customized a new page template

Slimer template is very sample, make a new template file under app/Templates and use Twig syntix to extends adminlte Slimer base template as below

```HTML
{% extends 'adminlte/layout/base.html.twig' %}

{% block stylesheets %}
<link rel="stylesheet" type="text/css" href="{{ base_url() }}/app/Static/css/bootstrap-table.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ base_url() }}/app/Static/css/bootstrap-table-filter-control.css"/>
{% endblock %}

{% block header_content %}
    {{ macros.page_header("Request form", "Submit form which validation") }}
{% endblock %}

 {% block admin_lte_breadcrumb %}
       <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="">Samples</li>
        <li class="active">Form</li>
      </ol>
 {% endblock %}

{% block page_content %}
	<!-- Default box -->

....................

```

Slimer defines several block in the base template, they can be overwrited accordingly.

- stylesheets
- header_content
- admin_lte_breadcrumb
- page_content
- javascripts
- ....