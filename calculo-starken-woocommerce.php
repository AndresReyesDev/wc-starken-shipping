<?php
/**
 * Plugin Name: Despacho vía Starken para WooCommerce
 * Plugin URI: https://andres.reyes.dev
 * Description: Cálculo de Despacho vía Starken para WooCommerce
 * Version: 2020.12.26
 * Author: AndresReyesDev
 * Contributors: AndresReyesDev
 * Author URI: https://andres.reyes.dev
 * License: MIT License
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: starken
 *
 * WC requires at least: 3.6.4
 * WC tested up to: 4.8
 *
 */

/*
MIT License

Copyright (c) 2017 Andrés Reyes Galgani (AndresReyesTech)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

if (!defined('WPINC')) {
    die;
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    function starken_shipping_method()
    {

        if (!class_exists('Starken_Shipping_Method')) {

            class Starken_Shipping_Method extends WC_Shipping_Method
            {

                public function __construct()
                {
                    $this->id = 'starken';
                    $this->method_title = __('Starken', 'starken');
                    $this->method_description = __('Cálculo de Despacho vía Starken', 'starken');
                    $this->availability = 'including';
                    $this->countries = array(
                        'CL' // Sólo para Chile
                    );

                    $this->init();
                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Starken', 'starken');
                    $this->api = isset($this->settings['api']) ? $this->settings['api'] : __('', 'starken');
                    $this->origen = isset($this->settings['origen']) ? $this->settings['origen'] : __('1', 'starken');
                    $this->enable_default = isset($this->settings['enable_default']) ? $this->settings['enable_default'] : 'yes';
                    $this->default_weight = isset($this->settings['default_weight']) ? $this->settings['default_weight'] : __('250', 'starken');
                    $this->default_height = isset($this->settings['default_height']) ? $this->settings['default_height'] : __('25', 'starken');
                    $this->default_width = isset($this->settings['default_width']) ? $this->settings['default_width'] : __('25', 'starken');
                    $this->default_length = isset($this->settings['default_length']) ? $this->settings['default_length'] : __('25', 'starken');
                    $this->enable_round = isset($this->settings['enable_round']) ? $this->settings['enable_round'] : 'yes';
                    $this->round_type = isset($this->settings['round_type']) ? $this->settings['round_type'] : __('1', 'starken');
                }

                function init()
                {
                    $this->init_form_fields();
                    $this->init_settings();

                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                function init_form_fields()
                {

                    $weight_unit = get_option('woocommerce_weight_unit');
                    $dimension_unit = get_option('woocommerce_dimension_unit');

                    $this->form_fields = array(
                        'enabled' => array(
                            'title' => __('Activo', 'starken'),
                            'type' => 'checkbox',
                            'description' => __('Activar método de despacho.', 'starken'),
                            'default' => 'yes'
                        ),
                        'title' => array(
                            'title' => __('Título', 'starken'),
                            'type' => 'text',
                            'description' => __('El título será mostrado en el despacho', 'starken'),
                            'default' => __('Starken', 'starken')
                        ),
                        'api' => array(
                            'title' => __('API', 'starken'),
                            'type' => 'text',
                            'description' => __('Las consultas requieren de una API para evitar abusos... y es <strong>GRATIS!</strong>. Sólo regístrate en <a href="//www.anyda.xyz/" target="_blank">starken.swo</a>', 'starken'),
                            'default' => __('', 'starken')
                        ),
                        'origen' => array(
                            'title' => __('Sucursal de Origen', 'starken'),
                            'type' => 'select',
                            'description' => __('Seleccione la sucursal desde donde se realizarán los despachos. Esta servirá como base para los cálculos del sistema.', 'starken'),
                            'default' => __('Starken', 'starken'),
                            'options' => $this->obtener_origen()
                        ),
                        'enable_default' => array(
                            'title' => __('Activar Tamaño Mínimo', 'starken'),
                            'type' => 'checkbox',
                            'description' => __('En caso de que el producto no tenga tamaño tomará estos valores.', 'starken'),
                            'default' => 'yes'
                        ),
                        'default_weight' => array(
                            'title' => __('Peso por Defecto', 'starken'),
                            'type' => 'text',
                            'description' => __('Sólo números y signo (de ser necesario) en ' . $weight_unit . '', 'starken'),
                            'default' => __('250', 'starken')
                        ),
                        'default_height' => array(
                            'title' => __('Altura por Defecto', 'starken'),
                            'type' => 'text',
                            'description' => __('Sólo números y signo (de ser necesario) en ' . $dimension_unit . '', 'starken'),
                            'default' => __('25', 'starken')
                        ),
                        'default_width' => array(
                            'title' => __('Ancho por Defecto', 'starken'),
                            'type' => 'text',
                            'description' => __('ólo números y signo (de ser necesario) en ' . $dimension_unit . '', 'starken'),
                            'default' => __('25', 'starken')
                        ),
                        'default_length' => array(
                            'title' => __('Largo por Defecto', 'starken'),
                            'type' => 'text',
                            'description' => __('ólo números y signo (de ser necesario) en ' . $dimension_unit . '', 'starken'),
                            'default' => __('25', 'starken')
                        ),
                        'enable_round' => array(
                            'title' => __('Activar Redondear Despacho', 'starken'),
                            'type' => 'checkbox',
                            'description' => __('Permite redondear hacia arriba los despachos. Puede elegir redondear Centenas o Millar. Ej: $12.345 puede convertirse en $12.400.- ó $13.000.-', 'starken'),
                            'default' => 'yes'
                        ),
                        'round_type' => array(
                            'title' => __('Tipo de Redondeo', 'starken'),
                            'type' => 'select',
                            'description' => __('Seleccione el tipo de redondeo que desea activar', 'starken'),
                            'options' => array(
                                '1' => 'Redondear Centena (Convertir $12.345 en $12.400.-)',
                                '2' => 'Redondear Millar (Convertir $12.345 en $13.000.-)',
                            )
                        ),

                    );
                }

                public function obtener_origen()
                {
                    $json_key = 'starken_origin_key';
                    $_json_expiration = 60 * 60 * 24; // 1 day
                    $key = $json_key;

                    if ( $data = get_transient($key) ) {
                        // Already in cache - do nothing
                    } else {
                        $json_string = 'https://www.anyda.xyz/origen';
                        $jsondata = wp_remote_get($json_string);
                        $data = json_decode( wp_remote_retrieve_body($jsondata), true );
                    
                        // IF IT IS NEW, SET THE TRANSIENT FOR NEXT TIME
                        set_transient($key, $data, $_json_expiration);
                    }

                    return $data;
                }

                public function calculate_shipping($package = array())
                {
                    $apiKey = $this->api;
                    $origen = $this->origen;
                    $destino = $package['destination']['state'];
                    $support = 0;

                    $weight = 0;
                    $height = 0;
                    $width = 0;
                    $length = 0;

                    $con = 0;

                    foreach ($package['contents'] as $item_id => $values) {
                        $_product = $values['data'];

                        if ($_product->has_dimensions()) {
                            if ($_product->get_width() > $width) {
                                $width = $_product->get_width();
                            }
                            if ($_product->get_length() > $length) {
                                $length = $_product->get_length();
                            }
                            $weight = $weight + ($_product->get_weight() * $values['quantity']);
                            $height = $height + ($_product->get_height() * $values['quantity']);
                        } else {
                            if ($this->enable_default == 'yes') {
                                if ($_product->default_width > $width) {
                                    $width = $_product->default_width;
                                }
                                if ($_product->default_length > $length) {
                                    $length = $_product->default_length;
                                }
                                $weight = $weight + ($this->default_weight * $values['quantity']);
                                $height = $height + ($this->default_height * $values['quantity']);
                            }
                        }
                    }

                    $weight = wc_get_weight($weight, 'kg');

                    $post_data = array(
                        'api' => $apiKey,
                        'support' => $support,
                        'origen' => $origen,
                        'destination' => $destino,
                        'weight' => $weight,
                        'height' => $height,
                        'width' => $width,
                        'length' => $length
                    );

                    $service_url = 'https://www.anyda.xyz/tarifa';

                    $result = wp_remote_post($service_url, array(
                            'method' => 'POST',
                            'body' => http_build_query($post_data)
                        )
                    );

                    if (is_wp_error($result)) {
                        return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
                    }

                    $decoded = json_decode($result['body']);
                    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
                        return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
                    }

                    if ($this->enable_round == 'yes') {
                        if ($this->round_type == 1) {
                            $round = (int)$decoded->tarifa;
                            $valorFinal1[$con] = ceil($round / 100) * 100;
                        } else {
                            $round = (int)$decoded->tarifa;
                            $valorFinal1[$con] = ceil($round / 1000) * 1000;
                        }
                    } else {
                        $valorFinal1[$con] = (int)$decoded->tarifa;
                    }

                    $valorFinal = (int)$decoded->tarifa;

                    if (empty($valorFinal) || !isset($valorFinal) || !is_numeric($valorFinal)) {
                        return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
                    }

                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $valorFinal,
                        'calc_tax' => 'per_item'
                    );

                    $this->add_rate($rate);
                }
            }
        }
    }

    add_action('woocommerce_shipping_init', 'starken_shipping_method');

    function add_starken_shipping_method($methods)
    {
        $methods[] = 'Starken_Shipping_Method';
        return $methods;
    }

    function starken_obtener_destino()
    {
        $json_key = 'starken_destination_key';
        $_json_expiration = 60 * 60 * 24; // 1 day
        $key = $json_key;

        if ( $data = get_transient($key) ) {
            // Already in cache - do nothing
        } else {
            $json_string = 'https://www.anyda.xyz/destino';
            $jsondata = wp_remote_get($json_string);
            $data = json_decode( wp_remote_retrieve_body($jsondata), true );
        
            // IF IT IS NEW, SET THE TRANSIENT FOR NEXT TIME
            set_transient($key, $data, $_json_expiration);
        }

        return $data;
    }

    function starken_comunas_de_chile($states)
    {
        $states['CL'] = starken_obtener_destino();
        return $states;
    }

    add_filter('woocommerce_shipping_methods', 'add_starken_shipping_method');
    add_filter('woocommerce_states', 'starken_comunas_de_chile');
}