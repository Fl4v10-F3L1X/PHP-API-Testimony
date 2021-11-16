<?php
    namespace App\Model\Entity;

    class Organization{
        /**
         * ID da organização
         * @var integer
         */
        public $id = 1;

        /**
         * Nome da Organização
         * @var string
         */
        public $name = 'Flávio WebDev';

        /**
         * Site da Organização
         * @var string 
         */
        public $site = 'https://github.com/Fl4v10';

        /**
         * Descrição da Organização
         * @var string
         */
        public $description = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus et tempora nihil distinctio explicabo dolorem reiciendis unde consequatur ipsum repellat, reprehenderit ex fugit. Quam minus dolor doloremque similique optio sit?';
    }
