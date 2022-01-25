<?php
namespace App;
use DI\Container;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Application
{
    private static $instance;

    public static function getInstance(): ?App
    {
        if(self::$instance !== NULL){
            return self::$instance;
        }
        $container = self::initializeContainer();
        AppFactory::setContainer($container);
        self::$instance = AppFactory::create();
        return self::$instance;
    }

    /**
     * @return Container
     */
    private static function initializeContainer(): Container
    {
        $container = new Container();
        self::registerServices($container);
        return $container;
    }

    /**
     * @param Container $container
     * @return void
     */
    private static function registerServices(Container $container): void
    {
        $container->set(ValidatorInterface::class, function (ContainerInterface $container) {
            return new RecursiveValidator(
                new ExecutionContextFactory(
                    new Translator('en')
                ),
                new LazyLoadingMetadataFactory(
                    new AnnotationLoader(
                        new AnnotationReader(
                            new DocParser()
                        )
                    )
                ),
                new ConstraintValidatorFactory()
            );
        });
    }
}