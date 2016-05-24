<?php

namespace Isi\PersonaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IdentGenerosControllerTest extends WebTestCase
{
    public function testFormulario()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/formulario');
    }

    public function testEdicion()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/edicion');
    }

    public function testBorrar()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/borrar');
    }

}
