<?php

class Token
{
    public static function generateByCart(Cart $cart)
    {
        $key = [
            $cart->id,
            $cart->id_customer,
            $cart->getOrderTotal(),
        ];

        return Tools::encrypt(json_encode($key));
    }
}