<?php
// Puxa a chave API universal pros checkouts
include_once('../nlo-config.php');
$upsell = "../up2";

//Os dados gerados na página anterior são repassados pra essa e aqui são reutilizados.
//Isso ajuda a ter uma noção maior da conversão de upsell sem necessariamente olhar pro parâmetro 'upsell' na aba de utms da gateway, só batendo o olho


// Função essencial pra gerar cpf. Essa função precisa ficar fora do if. Serve pra calcular o dígito verificador
function calcularDigitoVerificador($cpf, $digito) {
	$soma = 0;
	$multiplicador = ($digito === 1) ? 10 : 11;
	for ($i = 0; $i < strlen($cpf); $i++) {
		$soma += (int)$cpf[$i] * ($multiplicador - $i);
	}

	$resto = $soma % 11;
	$digitoVerificador = ($resto < 2) ? 0 : 11 - $resto;

	return $digitoVerificador;
};

// Pega CPF (ou "document") via GET
if(isset($_GET['cpf']) && !empty($_GET['cpf'])){
	$cpf = $_GET['cpf'];
} elseif(isset($_GET['document']) && !empty($_GET['document'])) {
	$cpf = $_GET['document'];
} else {
    // Gera o CPF
    $cpf = "";
    
    // Gera os 9 primeiros dígitos aleatórios
    for ($i = 0; $i < 9; $i++) {
        $cpf .= rand(0, 9);
    };
    
    // Calcula os 2 dígitos verificadores
    $cpf .= calcularDigitoVerificador($cpf, 1);  // Calcula o 1º dígito verificador
    $cpf .= calcularDigitoVerificador($cpf, 2);  // Calcula o 2º dígito verificador
};

// Pega nome (ou "name") via GET
if(isset($_GET['nome']) && !empty($_GET['nome'])){
	$nomeCompleto = $_GET['nome'];
	$nome = $nomeCompleto;
} elseif(isset($_GET['name']) && !empty($_GET['name'])) {
	$nomeCompleto = $_GET['name'];
	$nome = $nomeCompleto;
} else {
    //GERAR NOME SE NÃO HOUVER NA URL
    // Arrays de nomes e sobrenomes
    $primeirosNomes = [
        "Ana", "João", "Maria", "Carlos", "Lucas", "Sofia", "Pedro", "Fernanda", "Eduardo", 
        "Isabela", "Gustavo", "Beatriz", "Ricardo", "Patrícia", "Roberto", "Juliana", 
        "Felipe", "Larissa", "Thiago", "Julio", "Cláudia", "Vitor", "Bruna", "Renato", "Vanessa"
    ];
    $sobrenomes = [
        "Silva", "Santos", "Oliveira", "Costa", "Pereira", "Almeida", "Martins", "Rodrigues", 
        "Melo", "Dias", "Souza", "Nascimento", "Barbosa", "Araujo", "Cavalcanti", "Campos", 
        "Pinto", "Lima", "Carvalho", "Gomes", "Ferreira", "Ribeiro", "Castro", "Mendes", 
        "Azevedo", "Fernandes", "Morais", "Vieira", "Faria", "Pimentel"
    ];
    $terceirosNomes = [
        "Lima", "Gomes", "Ribeiro", "Ferreira", "Mendes", "Azevedo", "Carvalho", "Fernandes", 
        "Figueiredo", "Moura", "Rocha", "Teixeira", "Silveira", "Lopes", "Santana", "Pereira", 
        "Alves", "Sá", "Castro", "Machado", "Fontes", "Mello", "Pimentel", "Tavares", "Barreto", 
        "Assis", "Leal", "Cunha", "Rezende", "Borges"
    ];
    
    // Gerar nome aleatório
    $primeiroNome = $primeirosNomes[array_rand($primeirosNomes)];
    $sobrenome = $sobrenomes[array_rand($sobrenomes)];
    
    // Garantir que o terceiro nome não seja igual ao sobrenome
    do {
        $terceiroNome = $terceirosNomes[array_rand($terceirosNomes)];
    } while ($terceiroNome == $sobrenome);
    
    $nomeCompleto = $primeiroNome . " " . $sobrenome . " " . $terceiroNome;
    $nome = $nomeCompleto;
};

// Gerar e-mail de acordo com o nome
$nomeFormatado = strtolower(preg_replace('/\s+/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nomeCompleto)));
$dataNascimento = str_pad(rand(1, 31), 2, '0', STR_PAD_LEFT) . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
$emailDomains = ["@gmail.com", "@hotmail.com", "@outlook.com", "@yahoo.com", "@icloud.com"];
$dominio = $emailDomains[array_rand($emailDomains)];

$email = $nomeFormatado . $dataNascimento . $dominio;


// Pega número de telefone (ou "phone") via GET
if(isset($_GET['telefone']) && !empty($_GET['telefone'])){
	$telefone = $_GET['telefone'];
} elseif(isset($_GET['phone']) && !empty($_GET['phone'])) {
	$telefone = $_GET['phone'];
} else {
    //GERA TELEFONE SE NÃO HOUVER NA URL
    // Gerar um DDD aleatório entre 11 e 99
    $ddd = rand(11, 99);
    
    // Decidir se o número terá o dígito 9 ou não (50% de chance)
    $comDigito9 = rand(0, 1) === 1;
    
    // Se o número tiver o dígito 9, gerar o número com 9 + 8 dígitos
    if ($comDigito9) {
        $telefone = $ddd . "9" . rand(10000000, 99999999); // 8 dígitos + 9
    } else {
        $telefone = $ddd . rand(10000000, 99999999); // 8 dígitos sem 9
    };
};

/*
// Exibir resultados
echo "Nome: " .     $nomeCompleto . "<br>";
echo "E-mail: " . $email . "<br>";
echo "CPF Gerado: " . $cpf . "<br>";
echo "Telefone: " . $telefone;
*/
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Erro no Pagamento - Tente Novamente</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet"
    />

    <?php
    echo $pixel_scripts;
    
    if($track_fb_pixel == 1){ ?>
    <!-- Meta Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src='https://connect.facebook.net/en_US/fbevents.js';
      s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script');
    
      fbq('init', '<?php echo $fb_pixel; ?>'); // Substitua com o ID do seu pixel
      fbq('track', 'PageView');
    </script>
    <noscript>
      <img height="1" width="1" style="display:none"
           src="https://www.facebook.com/tr?id=<?php echo $fb_pixel; ?>&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Meta Pixel Code -->
    <?php }; ?>

    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: Roboto, sans-serif;
        background-color: #fef2f2;
      }

      .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 16px;
        text-align: center;
        box-sizing: border-box;
      }

      .error-icon {
        width: 80px;
        height: 80px;
        margin: 20px auto;
        background: #dc2626;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: shake 0.5s ease-in-out;
      }

      .error-icon::before {
        content: "⚠";
        color: white;
        font-size: 40px;
        font-weight: bold;
      }

      @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
      }

      h1 {
        font-family: Poppins, sans-serif;
        font-size: 1.8em;
        color: #dc2626;
        margin-bottom: 20px;
      }

      p {
        font-size: 1em;
        color: #374151;
        margin-bottom: 20px;
        text-align: left;
        max-width: 90%;
        margin-left: auto;
        margin-right: auto;
      }

      .error-box {
        background: #fee2e2;
        border: 2px solid #fca5a5;
        border-radius: 12px;
        padding: 20px;
        margin: 20px auto;
        max-width: 90%;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
      }

      .error-title {
        color: #dc2626;
        font-size: 1.2em;
        font-weight: 600;
        margin-bottom: 10px;
        font-family: Poppins, sans-serif;
      }

      .error-message {
        color: #991b1b;
        font-size: 0.95em;
        line-height: 1.5;
        margin-bottom: 15px;
      }

      .buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
      }

      .button {
        background: #ea580c;
        border-radius: 4px;
        border: none;
        color: #ffffff;
        padding: 16px 32px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.5s ease-in-out;
        text-decoration: none;
        font-family: Montserrat, sans-serif;
        margin-top: 20px;
        width: 100%;
        max-width: 90%px;
      }

      .button:hover {
        background: #c2410c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
      }

      .reject-link {
        color: #6b7280;
        text-decoration: none;
        font-size: 14px;
        margin-top: 10px;
        cursor: pointer;
        display: block;
      }

      .reject-link:hover {
        text-decoration: underline;
      }

      @media (max-width: 640px) {
        .container {
          padding: 16px;
          margin: 0;
          width: 100%;
        }

        .custom-value-input {
          padding: 16px;
          margin: 0;
          border-radius: 12px;
        }

        .value-input {
          font-size: 20px;
          padding: 12px;
        }

        .generate-button {
          margin: 16px 0;
          width: 100%;
        }
      }

      .input-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        margin: 16px auto;
        width: 100%;
        max-width: 100%;
        padding: 0 16px;
        box-sizing: border-box;
      }

      .preset-values {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
      }

      .preset-button:last-child {
        grid-column: auto;
      }

      .preset-button {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        background: #fff;
        color: #333;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      }

      .preset-button .amount {
        color: #ea580c;
        font-size: 16px;
        font-weight: 600;
        font-family: "Poppins", sans-serif;
      }

      .preset-button span:last-child {
        color: #666;
        font-size: 12px;
      }

      .preset-button:hover {
        border-color: #ea580c;
        background: #fff7ed;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(234, 88, 12, 0.15);
      }

      .preset-button.selected {
        border-color: #ea580c;
        background: #fff7ed;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.2);
      }

      .custom-value-input {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 24px;
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        box-sizing: border-box;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      }

      .custom-value-input h2 {
        text-align: center;
        color: #000000;
        font-size: 22px;
        margin: 0 0 24px;
        font-weight: 600;
        font-family: Poppins, sans-serif;
      }

      .value-input {
        width: 100%;
        max-width: 100%;
        padding: 16px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-size: 24px;
        text-align: center;
        color: #000000;
        font-weight: 500;
        font-family: "Poppins", sans-serif;
        box-sizing: border-box;
        background: #f8f8f8;
      }

      .value-input:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.15);
        background: #ffffff;
      }

      .value-input::placeholder {
        color: #939191;
        font-weight: 400;
      }

      .shortcuts {
        text-align: center;
      }

      .shortcuts-label {
        color: #939191;
        font-size: 14px;
        margin: 16px 0 8px;
        font-weight: 500;
        font-family: Roboto, sans-serif;
      }

      .shortcuts-grid {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
        /* padding: 0 8px; */
        box-sizing: border-box;
      }

      .shortcut-chip {
        padding: 8px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        background: #fff;
        color: #000000;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: Montserrat, sans-serif;

        flex: 1 1 calc(33.333% - 0.5rem); /* 3 por linha, compensando o gap */
        min-width: 6.5em;   /* aproximadamente 96px se fonte for 16px */
        max-width: 12.5em; /* aproximadamente 200px */
        padding: 0.75em;   /* padding interno proporcional ao tamanho da fonte */
        box-sizing: border-box;
      }

      .shortcut-chip:hover {
        border-color: #ea580c;
        background: #fff7ed;
        color: #ea580c;
      }

      .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 12px;
        text-align: center;
        background: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #ffebeb;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.1);
        font-family: Roboto, sans-serif;
      }

      .generate-button {
        width: 100%;
        max-width: 100%;
        margin: 0;
        background: #ea580c;
        border-radius: 8px;
        border: none;
        color: #ffffff;
        padding: 16px 32px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: Montserrat, sans-serif;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.2);
      }

      .generate-button:hover {
        background: #c2410c;
        box-shadow: 0 12px 32px rgba(234, 88, 12, 0.25);
        transform: translateY(-2px);
      }

      .generate-button:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
      }

      .pix-area {
        width: 100%;
        max-width: 90%;
        margin: 20px auto;
      }

      .pix-container {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      }

      .pix-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
      }

      .pix-header h3 {
        margin: 0;
        font-size: 16px;
        color: #333;
        font-weight: 600;
      }

      .pix-icon {
        width: 24px;
        height: 24px;
      }

      .pix-code-container {
        position: relative;
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
      }

      .pix-code {
        width: 90%;
        height: 60px;
        padding: 8px 12px;
        background: #f8f8f8;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-family: monospace;
        font-size: 14px;
        resize: none;
        margin-bottom: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #333;
        letter-spacing: 0.5px;
      }

      .copy-button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        background: #ea580c;
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
      }

      .copy-button:hover {
        background: #c2410c;
      }

      .copy-icon {
        width: 20px;
        height: 20px;
      }

      .pix-info {
        margin: 0;
        font-size: 14px;
        color: #666;
        text-align: center;
        line-height: 1.4;
      }

      .value-input::-webkit-input-placeholder {
        color: #666;
      }

      .value-input::-webkit-outer-spin-button,
      .value-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      .value-input[type="number"] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
        appearance: textfield;
      }

      .currency-prefix {
        display: none;
      }

      .loading-container {
        width: 100%;
        max-width: 300px;
        margin: 20px auto;
        text-align: center;
      }

      .loading-content {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      }

      .loading-spinner {
        width: 40px;
        height: 40px;
        margin: 0 auto 16px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #ea580c;
        border-radius: 50%;
        animation: spin 1s linear infinite;
      }

      .loading-content p {
        margin: 0;
        color: #666;
        font-size: 14px;
      }

      @keyframes spin {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      @media (max-width: 640px) {
        .custom-value-input {
          padding: 16px;
        }

        .custom-value-input h2 {
          font-size: 20px;
          margin: 0 0 16px;
        }

        .value-input {
          font-size: 20px;
          padding: 12px;
        }

        .shortcuts-label {
          margin: 12px 0 8px;
        }

        .generate-button {
          padding: 14px 24px;
          font-size: 15px;
        }

        .error-icon {
          width: 60px;
          height: 60px;
        }

        .error-icon::before {
          font-size: 30px;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="error-icon"></div>

      <h1>Ops! Algo deu errado<br />com seu pagamento</h1>

      
      <div class="error-box">
        <div class="error-title">❌ Pagamento não processado</div>
        <div class="error-message">
          Infelizmente houve um problema ao processar seu pagamento. Isso pode ter acontecido por diversos motivos como instabilidade na conexão, problemas temporários no sistema bancário ou dados incorretos.
        </div>
        <div class="error-message">
          <strong>Não se preocupe!</strong> Você pode tentar novamente agora mesmo. Nenhum valor foi cobrado da sua conta.
        </div>
      </div>

      <p><strong>⚠️ NÃO DESISTA<!-- DESSA OPORTUNIDADE -->!</strong></p>

      <p>
        Sabemos que problemas técnicos podem ser frustrantes, mas não deixe que isso impeça você de fazer a diferença. Sua doação é muito importante para nós!
      </p>

      <p>
        💗 Tente novamente agora - geralmente o problema se resolve na segunda tentativa.
        <!-- Nossa equipe técnica trabalha constantemente para garantir-->Estamos sempre cuidando para
        <!-- a melhor experiência de doação. -->
        que sua contribuição chegue com todo carinho e segurança.
      </p>

      <p>👉 Clique em "Tentar Novamente" e<br />complete sua doação!</p>

      <p>Escolha um dos valores abaixo para tentar novamente: 🙏</p>

      <div class="input-area">
        <div class="custom-value-input">
          <h2
            style="
              text-align: center;
              color: #333;
              font-size: 20px;
              margin: 0 0 24px;
              font-weight: 600;
            "
          >
            Digite o valor para tentar novamente
          </h2>
          <div class="value-input-wrapper">
            <input
              type="tel"
              inputmode="numeric"
              id="customValue"
              placeholder="R$ 0,00"
              class="value-input"
              pattern="[0-9]*"
            />
          </div>
          <div class="shortcuts">
            <p class="shortcuts-label">Atalhos rápidos:</p>
            <div class="shortcuts-grid">
              <button onclick="fillAmount(20)" class="shortcut-chip">
                R$ 20
              </button>
              <button onclick="fillAmount(30)" class="shortcut-chip">
                R$ 30
              </button>
              <button onclick="fillAmount(50)" class="shortcut-chip">
                R$ 50
              </button>
              <button onclick="fillAmount(75)" class="shortcut-chip">
                R$ 75
              </button>
              <button onclick="fillAmount(100)" class="shortcut-chip">
                R$ 100
              </button>
              <button onclick="fillAmount(200)" class="shortcut-chip">
                R$ 200
              </button>
              <button onclick="fillAmount(300)" class="shortcut-chip">
                R$ 300
              </button>
              <button onclick="fillAmount(400)" class="shortcut-chip">
                R$ 400
              </button>
              <button onclick="fillAmount(500)" class="shortcut-chip">
                R$ 500
              </button>
              <button onclick="fillAmount(1000)" class="shortcut-chip">
                R$ 1000
              </button>
            </div>
          </div>
        </div>
        <button onclick="generatePix()" class="generate-button">
          Tentar Novamente - Gerar PIX
        </button>
      </div>

      <div id="loadingPix" class="loading-container" style="display: none">
        <div class="loading-content">
          <div class="loading-spinner"></div>
          <p>Gerando código PIX...</p>
        </div>
      </div>

      <div id="pixArea" style="display: none" class="pix-area">
        <div class="pix-container">
          <div class="pix-header">
            <img
              src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIwLjI1IDQuNUg1LjI1QzQuMDA3MzYgNC41IDMgNS41MDczNiAzIDYuNzVWMTcuMjVDMyAxOC40OTI2IDQuMDA3MzYgMTkuNSA1LjI1IDE5LjVIMjAuMjVDMjEuNDkyNiAxOS41IDIyLjUgMTguNDkyNiAyMi41IDE3LjI1VjYuNzVDMjIuNSA1LjUwNzM2IDIxLjQ5MjYgNC41IDIwLjI1IDQuNVoiIHN0cm9rZT0iIzM1Y2I0YSIgc3Ryb2tlLXdpZHRoPSIxLjUiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPHBhdGggZD0iTTMgOC4yNUgyMi41IiBzdHJva2U9IiMzNWNiNGEiIHN0cm9rZS13aWR0aD0iMS41IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KPC9zdmc+Cg=="
              alt="PIX Icon"
              class="pix-icon"
            />
            <h3>Código PIX gerado</h3>
          </div>
          <div class="pix-code-container">
            <textarea id="pixCode" readonly class="pix-code"></textarea>
            <button onclick="copyPixCode()" class="copy-button">
              <img
                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIwIDlIMTFDOS44OTU0MyA5IDkgOS44OTU0MyA5IDExVjIwQzkgMjEuMTA0NiA5Ljg5NTQzIDIyIDExIDIySDIwQzIxLjEwNDYgMjIgMjIgMjEuMTA0NiAyMiAyMFYxMUMyMiA5Ljg5NTQzIDIxLjEwNDYgOSAyMCA5WiIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIxLjUiLz4KPHBhdGggZD0iTTUgMTVIM0MyLjQ0NzcyIDE1IDIgMTQuNTUyMyAyIDE0VjNDMiAyLjQ0NzcyIDIuNDQ3NzIgMiAzIDJIMTRDMTQuNTUyMyAyIDE1IDIuNDQ3NzIgMTUgM1Y1IiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjEuNSIvPgo8L3N2Zz4K"
                alt="Copy"
                class="copy-icon"
              />
              Copiar código
            </button>
          </div>
          <p class="pix-info">
            Após copiar o código, abra o app do seu banco e escolha a opção PIX
            Copia e Cola
          </p>
        </div>
      </div>
    </div>

    <script>
		const upsellUrl = "<?php echo $upsell; ?>";
		// Dados predefinidos para gerar o PIX automaticamente
		const nomePreDefinido = "<?php echo $nomeCompleto; ?>"; // Nome do cliente
		const emailPreDefinido = "<?php echo $email; ?>";
		const cpfPreDefinido = "<?php echo $cpf; ?>";
		const telefonePreDefinido = "<?php echo $telefone; ?>"; // Telefone do cliente (sem máscara)
        let id_transacao = null;
        let valor_transacao = null;

      // Funções principais primeiro
      function setAmount(value, event) {
        document.querySelectorAll(".preset-button").forEach((button) => {
          button.classList.remove("selected");
        });
        if (event) {
          event.target.closest(".preset-button").classList.add("selected");
        }

        document.getElementById("customValue").value = "";
        const amountInCents = value * 100;
        document.querySelector(".generate-button").dataset.selectedAmount =
          amountInCents;
      }

    function validateCustomValue(value) {
      const numValue = unformatCurrency(value);
      if (numValue < 15) {
        const message = document.createElement("div");
        message.style.color = "#dc3545";
        message.style.fontSize = "14px";
        message.style.marginTop = "8px";
        message.style.textAlign = "center";
        message.textContent = "O valor mínimo é R$ 15,00 devido às taxas";
    
        const existingMessage = document.querySelector(".error-message.js-error");
        if (existingMessage) {
          existingMessage.remove();
        }
    
        message.className = "error-message js-error";
        document.querySelector(".value-input-wrapper").appendChild(message);
        return false;
      }
    
      const existingMessage = document.querySelector(".error-message.js-error");
      if (existingMessage) {
        existingMessage.remove();
      }
      return true;
    }

      async function generatePix() {
        console.time("Tempo de geração do PIX");

        const customValue = document.getElementById("customValue").value;

        if (!customValue) {
          alert("Por favor, digite um valor para doar.");
          return;
        }

        const numValue = unformatCurrency(customValue);
        if (!validateCustomValue(customValue)) {
          return;
        }

        //const amountInCents = Math.round(numValue * 100);
       const amountInCents = Math.round((numValue - 0.02) * 100); //tira 2 centavos pra n dar pix repetido
       valor_transacao = amountInCents;

        const requestBody = {
          amount: amountInCents,
          description: "Pagamento via Pix",
          customer: {
            name: nomePreDefinido,
            document: cpfPreDefinido,
            phone: telefonePreDefinido,
            email: emailPreDefinido,
          },
          item: {
            title: "<?php echo $nome_up; ?>",
            price: amountInCents,
            quantity: 1,
          },
          paymentMethod: "PIX",
          utm:
            new URLSearchParams(window.location.search).toString() || "direct",
        };

        try {
          const button = document.querySelector(".generate-button");
          button.disabled = true;
          button.style.opacity = "0.7";

          document.querySelector(".input-area").style.display = "none";
          document.getElementById("pixArea").style.display = "none";
          document.getElementById("loadingPix").style.display = "block";

          console.log("Enviando requisição:", requestBody);

          const response = await fetch(
            "<?php echo $gateway_api; ?>",
            {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(requestBody),
            }
          );

          console.log("Status da resposta:", response.status);

          const data = await response.json();
          console.log("Dados recebidos:", data);

          document.getElementById("loadingPix").style.display = "none";

          if (response.ok && data.pixCode) {
            const pixCodeElement = document.getElementById("pixCode");
            pixCodeElement.value = data.pixCode;
            pixCodeElement.dataset.originalCode = data.pixCode;
            document.getElementById("pixArea").style.display = "block";

            if (data.transactionId) {
              startPixVerification(data.transactionId);
              id_transacao = data.transactionId;
            }
          } else {
            document.querySelector(".input-area").style.display = "flex";
            alert("Erro ao gerar o PIX. Por favor, tente novamente.");
          }
        } catch (error) {
          console.error("Erro detalhado:", error);
          document.querySelector(".input-area").style.display = "flex";
          document.getElementById("loadingPix").style.display = "none";
          alert("Erro ao gerar o PIX. Por favor, tente novamente.");
        } finally {
          console.timeEnd("Tempo de geração do PIX");
          const button = document.querySelector(".generate-button");
          button.disabled = false;
          button.style.opacity = "1";
        }
      }

      // Funções auxiliares depois
      function formatCurrency(value) {
        return new Intl.NumberFormat("pt-BR", {
          style: "currency",
          currency: "BRL",
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        }).format(value);
      }

      function unformatCurrency(value) {
        if (typeof value === "number") return value;
        return parseFloat(value.replace(/[^\d,]/g, "").replace(",", ".")) || 0;
      }

      function copyPixCode() {
        const pixCode = document.getElementById("pixCode");
        const textToCopy = pixCode.dataset.originalCode || pixCode.value;

        const temp = document.createElement("textarea");
        temp.value = textToCopy;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand("copy");
        document.body.removeChild(temp);

        const copyButton = document.querySelector(".copy-button");
        const originalText = copyButton.innerHTML;

        copyButton.innerHTML =
          '<img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIwLjI1IDYuNzVMOS43NSAxNy4yNUw0LjUgMTJMNS41NjI1IDEwLjkzNzVMOS43NSAxNS4wNjI1TDE5LjE4NzUgNS42ODc1TDIwLjI1IDYuNzVaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K" alt="Copied" class="copy-icon"> Copiado!';
        copyButton.style.background = "#28a745";

        setTimeout(() => {
          copyButton.innerHTML = originalText;
          copyButton.style.background = "#35cb4a";
        }, 2000);
      }

      async function startPixVerification(paymentId) {
        console.log("Iniciando verificação do PIX ID:", paymentId);
        let attempts = 0;
        const maxAttempts = 100; // 5 minutos (com intervalo de 3 segundos)

        const verificationInterval = setInterval(async () => {
          if (attempts >= maxAttempts) {
            console.log("Tempo máximo de verificação atingido");
            clearInterval(verificationInterval);
            return;
          }

          attempts++;

          try {
            console.log(
              "Tentativa",
              attempts,
              "de verificação para ID:",
              paymentId
            );

            const response = await fetch(`<?php echo $gateway_api; ?>?transactionId=${paymentId}`, {
              method: "GET",
              headers: {
                "Content-Type": "application/json",
              },
            });

            console.log("Status da verificação:", response.status);

            if (response.status === 404) {
              console.log("Aguardando confirmação do pagamento (404)...");
              return;
            }

            if (!response.ok) {
              console.log("Erro na verificação:", response.status);
              return;
            }

            const data = await response.json();
            console.log("Resposta completa da verificação:", data);

            if (data.status === "COMPLETED") {
              console.log("PIX Aprovado!")

                <?php if($track_fb_pixel == 1){ ?>
                	// Dispara o evento de compra do Facebook
                	if (typeof fbq !== 'undefined') {
                		fbq('track', 'Purchase', {
                			currency: 'BRL',
                			value: Number((valor_transacao / 100).toFixed(2)), // envia no formato 99.99 pro facebook
                			transaction_id: id_transacao
                		});
                		console.log('✅ Evento de compra enviado para o Facebook Pixel');
                	} else {
                		console.warn('⚠️ Facebook Pixel não disponível para enviar o evento de compra');
                	}
                <?php }; ?>

              console.log("Redirecionando...");
              clearInterval(verificationInterval);
					//const params = new URLSearchParams(window.location.search);
					//params.set("upsell", "1");
					//window.location.href = `${upsellUrl}?${params.toString()}`;
                    const upsell = new URL(upsellUrl, window.location.href);
                    const currentParams = new URLSearchParams(window.location.search);
                    
                    // Adiciona os parâmetros da página atual no upsellUrl (sem sobrescrever os já existentes no upsellUrl)
                    for (const [key, value] of currentParams.entries()) {
                    	if (!upsell.searchParams.has(key)) {
                    		upsell.searchParams.set(key, value);
                    	}
                    }

					//const nome = document.getElementById("name").value;
					//const telefone = document.getElementById("phone").value.replace(/\D/g, "");
                    //upsell.searchParams.set("nome", nome);
                    //upsell.searchParams.set("telefone", telefone);

                    //upsell.searchParams.delete("up");
                    //upsell.searchParams.set("upsell", "1"); // Força o parâmetro upsell=1, mesmo que já exista
                    upsell.searchParams.delete("valor");
                    window.location.href = upsell.toString(); // Redireciona
            } else {
              console.log("Aguardando pagamento...");
            }
          } catch (error) {
            console.error("Erro na verificação:", error);
            console.log("Tentando novamente em 3 segundos...");
          }
        }, 3000);

        // Limpa o intervalo após 5 minutos
        setTimeout(() => {
          clearInterval(verificationInterval);
        }, 5 * 60 * 1000);
      }

      function fillAmount(value) {
        const input = document.getElementById("customValue");
        input.value = formatCurrency(value);
        document.querySelector(".generate-button").dataset.selectedAmount =
          value * 100;
        validateCustomValue(input.value);
      }

      // Event Listeners por último
      document.addEventListener("DOMContentLoaded", function () {
        const input = document.getElementById("customValue");


        // Verificar se o parâmetro "valor" existe na URL
        const urlParams = new URLSearchParams(window.location.search);
        const valor = urlParams.get("valor");
    
        if (valor) {
            let value = parseFloat(valor).toFixed(2); // Garantir que o valor tenha 2 casas decimais
            input.value = formatCurrency(value); // Formatar e preencher o campo de entrada
            document.querySelector(".generate-button").dataset.selectedAmount = parseFloat(value) * 100; // Atualizar o valor no botão
            validateCustomValue(input.value); // Validar o valor inicial
        }


        if (input) {
          input.addEventListener("input", function (e) {
            document.querySelectorAll(".preset-button").forEach((button) => {
              button.classList.remove("selected");
            });

            let value = e.target.value.replace(/\D/g, "");
            value = (parseInt(value) / 100).toFixed(2);
            if (value) {
              e.target.value = formatCurrency(value);
              document.querySelector(
                ".generate-button"
              ).dataset.selectedAmount = parseFloat(value) * 100;
            }

            validateCustomValue(e.target.value);
          });
        }
      });
    </script>
  </body>
</html>
