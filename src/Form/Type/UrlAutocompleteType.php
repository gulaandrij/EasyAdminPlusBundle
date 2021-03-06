<?php

namespace Lle\EasyAdminPlusBundle\Form\Type;

use EasyCorp\Bundle\EasyAdminBundle\DataCollector\EasyAdminDataCollector;
use EasyCorp\Bundle\EasyAdminBundle\DependencyInjection\Compiler\EasyAdminConfigPass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Router;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;

class UrlAutocompleteType extends AbstractType
{

    private $router;
    private $configManager;

    public function __construct(Router $router, ConfigManager $configManager){
        $this->router = $router;
        $this->configManager = $configManager;
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getName()
    {
        return 'lle.easyadminplus.url_autocomplete';
    }



    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            "url" => "#",
            "path" => null,
            "class" => null,
            "value_filter" => null,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options){
        $view->vars['url'] = $options['url'];
        if($options['path']){
            $path = $options['path'];
            $path['params']['action'] = $path['params']['action'] ?? 'autocomplete';
            $path['route'] = $path['route'] ?? 'easyadmin';
            $view->vars['url'] = $this->router->generate($path['route'], $path['params']);
        }elseif($options['class']){
            $config = $this->configManager->getEntityConfigByClass($options['class']);
            $view->vars['url'] = $this->router->generate('easyadmin', ['action'=>'autocomplete', 'entity'=>$config['name']]);
        }

        $view->vars['value_filter'] = $options['value_filter'];
    }    
}
