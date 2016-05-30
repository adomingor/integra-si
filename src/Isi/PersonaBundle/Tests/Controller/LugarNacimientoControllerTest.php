<?php

namespace Isi\PersonaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LugarNacimientoControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/lugNacim');
    }

    public function testNuevo()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/lugNacim/nuevo');
    }

    public function testEdicion()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/lugNacim/edicion/{id}');
    }

    public function testBorrar()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/lugNacim/borrar/{id}');
    }

}
