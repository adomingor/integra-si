<?php

namespace Isi\PersonaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IdentidadGeneroControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/genero');
    }

    public function testFormulario()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/genero/formulario');
    }

    public function testEdicion()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/genero/edicion/{id}');
    }

    public function testBorrar()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/genero/borrar/{id}');
    }

}
