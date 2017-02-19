<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 19/02/2017
 * Time: 17:45
 */

namespace AppBundle\Service\Api;


interface ApiCrudInterface
{
    public function create();
    public function read($id);
    public function update($id);
    public function delete($id);
}