<?php
/**
 * Author: Shawn Chen
 * Desc: The AdminLTE navBar extension
 */
namespace Slimer\Html;

use Psr\Http\Message\ServerRequestInterface;
use Pimple\Container;

/**
 * slim/sidebar extension 
 *
 * @author slim
 *
 * @see 
 */
class NavBarExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    /**
     * @var container
     */
    
    protected $container;
    
    /**
     * @var
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'nav_bar_notifications',
                [$this, 'NotificationsFunction'],
                ['is_safe' => ['html'], 'needs_environment' => true]
                ),
            new \Twig_SimpleFunction(
                'nav_bar_tasks',
                [$this, 'TasksFunction'],
                ['is_safe' => ['html'], 'needs_environment' => true]
                ),
            new \Twig_SimpleFunction(
                'nav_bar_user_account',
                [$this, 'UserAccountFunction'],
                ['is_safe' => ['html'], 'needs_environment' => true]
                ),
            new \Twig_SimpleFunction(
                'user_avatar',
                [$this, 'AvatarFunction'],
                ['is_safe' => ['html'], 'needs_environment' => true]
                ),
        ];
    }
    
    public function NotificationsFunction(\Twig_Environment $environment)
    {
        if (!$this->container->has('notices')) {
            return "";
        }
        
        return $environment->render('adminlte/navBar/notifications.html.twig', [
            'notifications' => $this->container['notices'],
            'total'         => sizeof($this->container['notices'])
        ]);
    }
    
    public function TasksFunction(\Twig_Environment $environment)
    {
        
        if ($this->container->has('tasks') == null) {
            return "";
        }
        
        return $environment->render('adminlte/navBar/tasks.html.twig', [
            'tasks' => $this->container['tasks'],
            'total' => sizeof($this->container['tasks'])
        ]);
    }
    
    public function UserAccountFunction(\Twig_Environment $environment)
    {
        if ($this->container['session']->get('user') == null) {
            return "";
        }
        
        return $environment->render('adminlte/navBar/user.html.twig', ['user' => $this->container['session']->get('user')]);
    }
    
    public function AvatarFunction(\Twig_Environment $environment, $image, $alt = '', $class = 'img-circle')
    {
        if (!$image || !file_exists($image)) {
            $image = 'app/Static/img/avatar.png';
        }
        
        $image = "data:image/png;base64," . base64_encode(file_get_contents($image));
        
        return $environment
        ->createTemplate('<img src="{{ image }}" class="{{ class }}" alt="{{ alt }}"/>')
        ->render([
            'image' => $image,
            'class' => $class,
            'alt'   => $alt,
        ]);
    }
  
    
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Slimer/Html/NavBarExtension';
    }
}
