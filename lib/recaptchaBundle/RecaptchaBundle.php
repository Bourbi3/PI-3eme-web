<?php
namespace  widehaven\recaptchaBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RecaptchaBundle extends Bundle {

public function build(ContainerBuilder $container)
{
    parent::build($container);
    $container->addCompilerPass(new RecaptchaCompilerPass());

 //   parent::build($container); // TODO: Change the autogenerated stub
}


}

