<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'attr' => [
                'placeholder' => 'Entrez votre adresse e-mail',
                'title' => 'Saisissez une adresse e-mail valide',
            ],
            'label' => 'Adresse e-mail', // Libellé en français pour l'e-mail
        ])
        
        ->add('roles', ChoiceType::class, [
            'label' => 'Rôle',
            'multiple' => true,
            'expanded' => true,
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
                'Employé' => 'ROLE_EMPLOYE',
            ],
            'required' => true,
            'attr' => [
                'title' => 'Sélectionnez le(s) rôle(s) de l\'utilisateur',
            ],
            'label' => 'Rôle de l\'utilisateur', // Libellé en français pour les rôles
        ])
        
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => [
                'placeholder' => 'Entrez un mot de passe',
                'title' => 'Saisissez un mot de passe',
            ],
            'label' => 'Mot de passe', // Libellé en français pour le mot de passe
        ])
        
        ->add('address', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Entrez votre adresse',
                'title' => 'Saisissez votre adresse',
            ],
            'label' => 'Adresse', // Libellé en français pour l'adresse
        ])
        
        ->add('city', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Entrez votre ville',
                'title' => 'Saisissez votre ville',
            ],
            'label' => 'Ville', // Libellé en français pour la ville
        ])
        
        ->add('country', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Entrez votre pays',
                'title' => 'Saisissez votre pays',
            ],
            'label' => 'Pays', // Libellé en français pour le pays
        ])
        
        ->add('aboutUs', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Entrez des informations vous concernant',
                'title' => 'Saisissez des informations vous concernant',
            ],
            'label' => 'À propos de moi', // Libellé en français pour "À propos de moi"
        ])
        
        ->add('name', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Entrez votre nom',
                'title' => 'Saisissez votre nom',
            ],
            'label' => 'Nom', // Libellé en français pour le nom
        ])
        
        ->add('surname', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Entrez votre prénom',
                'title' => 'Saisissez votre prénom',
            ],
            'label' => 'Prénom', // Libellé en français pour le prénom
        ])
        
        ->add('newsletter', CheckboxType::class, [
            'required' => false,
            'label' => 'Inscrivez-vous à notre newsletter',
            'attr' => [
                'title' => 'Cochez pour vous inscrire à notre newsletter',
            ],
        ]);
        

        // Exclure le champ plainPassword si l'option exclude_password est définie à true
        if ($options['exclude_password']) {
            $builder->remove('plainPassword');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'exclude_password' => false, // Par défaut, ne pas exclure le champ du mot de passe
        ]);
    }
}