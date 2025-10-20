<?php
// ################## CONFIGURAÇÕES DE CHECKOUT ################

// $gateway_api = "https://app.duttyfy.com.br/api-pix/sua_chave_encriptada";
// Aqui é minha chave pra testes. Deixo aqui comentada pra quando preciso alterar algo e testar o funil ou checkout
//$gateway_api = "https://app.duttyfy.com.br/api-pix/Q6AErYsQaHmyPtgL23dep1y3gf5q9h8SAQ-Egq7Lc8Yu6j3Y-Cm7xFQdyN2omx6X3el6g_hkFEzdTifKA30VRw"; //NLO
$gateway_api = "https://app.duttyfy.com.br/api-pix/Q6AErYsQaHmyPtgL23dep1y3gf5q9h8SAQ-Egq7Lc8Yu6j3Y-Cm7xFQdyN2omx6X3el6g_hkFEzdTifKA30VRw"; //adriana

$icon_url = ""; //url ou caminho de favicon

if($_GET['up'] && !empty($_GET['up'])){
    $up = $_GET['up'];
    switch($up){
        /*
		case "1": //up1
        break;
		*/

        default: //front
            $front = 1;
        break;
    };
} else { //front
    $front = 1;
};

//configuração do front
if(isset($front) && $front == 1){
	$oferta = "dolly configuravel";
	$upsell = "../up1/"; //caminho ou URL pra onde o usuário é enviado após o pagamento
	if(isset($_GET['valor']) && !empty($_GET['valor']) && $_GET['valor'] >= 5){
		$valor = (float)$_GET['valor'];  // Resultado: 51.99 por exemplo
	} else {
		// se o valor não existir ou for vazio ou menor q 5, padrão é 20
		$valor = 20;
	};
	$logo_ativo = 1;
	$logo_url = "./images/logo.svg";
    $checkoutTitulo = "DOE AGORA 💚"; //titulo que aparece no checkout
    $checkoutDesc = "Sua contribuição ajuda muito!"; //$descrição que aparece no checkout
};

$nome_up = "Autopix Up1";
$nome_up2 = "Autopx Up2";
$nome_front = "Front Autopix";


// ################## CONFIGURAÇÕES DE TRACKEAMENTO ################

//aqui dentro, coloque seu código do pixel utmify. tecnicamente só precisa trocar ali na linha 16
$pixel_scripts = '
<script>
  window.pixelId = "xxxxxxxxxxxxxxxxxxxxxxxxxxxx";
  var a = document.createElement("script");
  a.setAttribute("async", "");
  a.setAttribute("defer", "");
  a.setAttribute("src", "https://cdn.utmify.com.br/scripts/pixel/pixel.js");
  document.head.appendChild(a);
</script>
';

$pixel_scripts = $pixel_scripts . '<script
  src="https://cdn.utmify.com.br/scripts/utms/latest.js"
  data-utmify-prevent-xcod-sck
  data-utmify-prevent-subids
  async
  defer
></script>
';

$track_fb_pixel = 0; //0 ou 1, pra marcação do pixel fb, fora da utmify
$fb_pixel = "12345678912345678"; //placeholder - aqui vc coloca o pixel fb
?>