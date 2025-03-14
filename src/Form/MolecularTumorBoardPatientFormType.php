<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Form;

use Doctrine\ORM\EntityManagerInterface;
use Pixiekat\FhirGenOpsApi\FhirApi;
use Pixiekat\MolecularTumorBoard\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event;
use Symfony\Component\Form\Extension\Core\Type as FormTypes;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class MolecularTumorBoardPatientFormType extends AbstractType {

  /**
   * The Pixiekat\FhirGenOpsApi\FhirApi definition.
   *
   * @var \Pixiekat\FhirGenOpsApi\FhirApi $fhirApi
   */
  private FhirApi $fhirApi;

  public function __construct(
    private Services\Interfaces\CancerTypeManagerInterface $cancerTypeManager,
    private EntityManagerInterface $entityManager,
    private TranslatorInterface $translator,
  ) {
    $this->fhirApi = new FhirApi();
  }

  public function buildForm(FormBuilderInterface $builder, array $options): void {
    //$patient = $this->fhirApi->getPatient('HG00403');
    $builder
      ->add('cancerType', FormTypes\ChoiceType::class, [
        'choices' => [
          'Breast Cancer' => [
            'Breast Carcinoma',
            'Breast Sarcoma',
          ],
          'Lung Cancer' => [
            'Lung Carcinoma',
            'Lung Sarcoma',
          ],
        ],
        'group_by' => fn($choice, $key, $value) => $key,
        'label' => $this->translator->trans('Select Cancer Type'),
        'required' => false,
      ])
      ->add('sequencingTests', FormTypes\ChoiceType::class, [
        'choices' => [
          'Sequencing Test 1' => 'Sequencing Test 1',
          'Sequencing Test 2' => 'Sequencing Test 2',
          'Sequencing Test 3' => 'Sequencing Test 3',
        ],
        'expanded' => true,
        'label' => $this->translator->trans('Select Sequencing Tests'),
        'multiple' => true,
        'required' => false,
      ])
      ->add('patient', FormTypes\TextType::class, [
        'attr' => [
          'placeholder' => 'HG00403',
        ],
        'constraints' => [
          new Assert\Length(['min' => 1, 'max' => 255]),
          new Assert\NotBlank(),
        ],
        'label' => $this->translator->trans('Patient Name'),
        'required' => true,
      ])
      ->add('patientHistory', FormTypes\TextareaType::class, [
        'label' => $this->translator->trans('Patient History'),
        'required' => false,
      ])
      ->add('submit', FormTypes\SubmitType::class, [
        'attr' => [
          'class' => 'btn-primary',
        ],
      ])
      ->add('cancel', FormTypes\SubmitType::class, [
        'attr' => [
          'class' => 'btn-link',
        ],
        'label' => $this->translator->trans('Start Over'),
      ])
      ->addEventListener(FormEvents::PRE_SUBMIT, function (Event\PreSubmitEvent $event): void {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data) {
          return;
        }

        /*
        if (!empty($data['patient'])) {
          $patient = $this->fhirApi->getPatient($data['patient']);
          if (!empty($patient)) {
            $form->get('patientHistory')->setData($patient['history']);
          }
          $form->get('patientHistory')->setData($patient['history']);
        }*/

        // checks whether the user has chosen to display their email or not.
        // If the data was submitted previously, the additional value that is
        // included in the request variables needs to be removed.
       // if (isset($user['showEmail']) && $user['showEmail']) {
            //$form->add('email', EmailType::class);
        //} else {
            //unset($user['email']);
            //$event->setData($user);
        //}
    })
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      // Configure your form options here
    ]);
  }

  private function loadChoices(array $values = null)
  {
      return [
          'Breast Cancer' => [
              'Breast Carcinoma',
              'Breast Sarcoma',
          ],
          'Lung Cancer' => [
              'Non-small cell lung cancer' => [
                  'Adrenocarcinoma',
                  'Squamous cell carcinoma',
              ],
              'Small cell lung cancer',
              'Mesothelioma',
          ],
      ];
  }
}
