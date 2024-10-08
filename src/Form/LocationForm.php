<?php

declare(strict_types=1);

namespace Drupal\location_finder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\location_finder\Service\Locations;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocationForm extends FormBase
{
    public function __construct(private readonly Locations $locationsService)
    {
    }

    public static function create(ContainerInterface $container): self
    {
        return new self($container->get(Locations::class));
    }

    public function getFormId(): string
    {
        return 'location_finder_location';
    }

    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $form['countryCode'] = [
            '#type' => 'textfield',
            '#title' => $this->t('countryCode'),
            '#size' => 60,
            '#maxlength' => 128,
            '#required' => true,
        ];

        $form['addressLocality'] = [
            '#type' => 'textfield',
            '#title' => $this->t('addressLocality'),
            '#size' => 60,
            '#maxlength' => 128,
            '#required' => true,
        ];

        $form['postalCode'] = [
            '#type' => 'textfield',
            '#title' => $this->t('postalCode'),
            '#size' => 60,
            '#maxlength' => 128,
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Send'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        try {
            $locations = $this->locationsService->findByAddress(
                $form_state->getValue('countryCode'),
                $form_state->getValue('addressLocality'),
                $form_state->getValue('postalCode')
            );
            $handledLocations = $this->locationsService->processLocations($locations);
            $yaml = $this->locationsService->convertToYaml($handledLocations);
            $this->messenger()->addMessage($yaml);
        } catch (\Exception $e) {
            $this->getLogger('location_finder')
                ->error(
                    'Get Locations request failed with error %error',
                    [
                        '%error' => $e->getMessage(),
                    ]
                );
            $this->messenger()->addError('Get Locations request failed');
        }
    }
}
