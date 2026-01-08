<?php
namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }

    private function excludeCurrentUserFromQuery(EntityRepository $er)
    {
        $qr = $er->createQueryBuilder('user');
        /** @var User $user */
        $user = $this->security->getUser();

        return $qr->andWhere(
            $qr->expr()->neq('user.id', $user->getId())
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'              => User::class,
            'autocomplete'       => true,
            'multiple'           => true,
            'by_reference'       => false,
            'tom_select_options' => [
                'delimiter'         => ',',
                'searchable_fields' => ['email', 'name'],
            ],
            'required'           => false,
            'query_builder'      => fn(EntityRepository $er)      => $this->excludeCurrentUserFromQuery($er),
        ]);
    }
}
