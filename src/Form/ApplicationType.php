<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Class ApplicationType.
 * 
 * @author Mickael Nambinintsoa <mickael.nambinintsoa07081999@gmail.com>
 */
class ApplicationType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un form.
     *
     * @param string $label
     * @param string $placehoder
     * @param array $options
     * 
     * @return array
     */
    public function getConfiguration($label, $placehoder, $options = [])
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placehoder
            ]
        ], $options);
    }
}