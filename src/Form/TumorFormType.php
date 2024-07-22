<?php
namespace Pixiekat\MolecularTumorBoard\Form;

use Pixiekat\MolecularTumorBoard\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as FormTypes;
use Symfony\Component\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class TumorFormType extends AbstractType {
  public function buildForm(FormBuilderInterface $builder, array $options): void {

    $builder
      ->add('name', FormTypes\TextType::class, [
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
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      'data_class' => Entity\Tumor::class,
    ]);
  }
}
