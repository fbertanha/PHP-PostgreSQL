<?php
#--------------------------------------------------------------------------------------------------------------------------------------------------------------
# Programa....: tstrelatorio1.php
# Descricao...: Relatório do TESTESQL
# Autor.......: João Maurício Hypólito - Copie mas diga quem fez.
# Objetivo....: Especificar e desenvolver um dos relatórios apresentados no texto do TESTESQL.
#               Este programa somente monta a ESTRUTURA Fundamental de como deve ser o relatório.
# Criacao.....: 2016-10-15
# Atualizacao.: 2016-10-15 - Primeira montagem.
# Modificação.: 2017-11-25 - Adaptado para funcionários. Felipe Bertanha
# Quantos funcionários têm mais de um plano de saúde ?
#--------------------------------------------------------------------------------------------------------------------------------------------------------------
# Algoritmo do programa
# Inicia as variáveis $passo e $salto
# Inicia a variavel $cordefundo com navajowhite se a $passo valer 1 ou 2 e WHITE quando $passo == 3
# Inicia a página com alinhamento padrão.
# Inicia SWITCH com valor da variável $passo.
#   Para $passo=='1'
#     monta um form recursivo com escolha da ordenação dos dados (variavel $ordem)
#     $passo='2' hidden
#   Para $passo=='2' ou $passo=='3'
#     Le os dados de usuários, tiposusuários e cidades em uma junção de 3 tabelas
#     monta o relatório lendo os dados com a ordenação escolhida no form anterior
#     Se $passo==2
#       entao
#         monta um form recursivo para escolha de versão a imprimir abrindo a emissao em uma nova aba do navegador.
#         $passo='3' hidden (escolhendo emitir para impressao a cor de fundo fica WHITE)
# FIM_DO_CASE_$PASSO
#--------------------------------------------------------------------------------------------------------------------------------------------------------------
# Carregando o ToolsKit (e executando as funções Gerais disponíveis no grupo de funções)
# fazendo a conexão com o banco de dados e recebendo as variáveis globalizadas da conex.
require_once("../../toolskit.php");
# Atrinbuindo valores em $passo e $salto.
$passo=(isset($_POST['passo']) ? $_POST['passo'] : '1');  // $passo recebe $_POST['passo'] (se houver), senão 1
$salto=(isset($_POST['salto'])? $_POST['salto']+1:'1');   // $salto recebe $_POST['salto']+1 (se houver), senão 1
# printf("Acao: $acao<br>Passo: $passo<br>Salto: $salto<br>");
$cordefundo = ($passo==3) ? "white" : "navajowhite" ;
# Iniciando a página
printf("<html xml:lang='pt-BR' lang='pt-BR' dir='ltr'>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n</head>\n");
printf("<body bgcolor='$cordefundo'>\n");
printf("<font face='tahoma' color=red><b>Relat&oacute;rio 02 do TESTESQL</b></font>\n");
# SWITCH CASE com a variável $passo
SWITCH (TRUE)
{ # 1.6.1
    #------------------------------------------------------------------------------------------------------------------------------------------------------------
    case ($passo==1):
    { # 1.6.1.1 Vamos montar o formulario para escolha da ordenação dos dados no relatório
        printf("<form action='relatorio002.php' method='post'>\n");
        printf("<input type='hidden' name='passo' value='2'>\n");
        printf("<input type='hidden' name='salto' value='$salto'>\n");
        printf("Quais são os funcionários que apresentaram com seus carros algum sinistro (acidente) nos últimos 12 meses e quantos acidentes foram registrados para cada um? A
data de referência deve ser informada por formulário<br>\n");
        printf("Escolha a ordena&ccedil;&atilde;o dos dados do relat&oacute;rio marcando um dos campos<br>\n");
        printf("<table>\n");
        printf("<tr><td>C&oacute;digo</td>             <td><INPUT TYPE=RADIO NAME='ordem' VALUE='f.cpfuncionario' CHECKED></td></tr>\n");
        printf("<tr><td>Nome</td>                      <td><INPUT TYPE=RADIO NAME='ordem' VALUE='f.txprenomes'></td></tr>\n");
        printf("<tr><td>Sobrenome</td>        <td><INPUT TYPE=RADIO NAME='ordem' VALUE='f.txsobrenome'></td></tr>\n");

        printf("<tr><td>Data de referência</td><td>De: <input type='text' name='diaini' size=2 maxlength=2>/<input type='text' name='mesini' size=2 maxlength=2>/<input type='text' name='anoini' size=4 maxlength=4> at&eacute;:<input type='text' name='diafim' size=2 maxlength=2>/<input type='text' name='mesfim' size=2 maxlength=2>/<input type='text' name='anofim' size=4 maxlength=4></td></tr>\n");
        # Montar o botão para Gerar a Listagem
        printf("<tr><td colspan=2>");
        # montar o botão de voltar UMA página, voltar para página de ABERTURA, "limpar" (RESET dos campos do form) e Gerar O Relatório
        printf("<input type='button' value='< P&aacute;gina' onclick='history.go(-1)'><input type='button' value='< Menu' onclick='history.go(-$salto)'><input type='button' value='< Sa&iacute;da' onclick='history.go(-($salto+1))'><input type='reset' value='Limpar'><input type=submit value='Gerar Listagem'>");
        printf("</td></tr>\n");
        printf("</table>\n");
        printf("</form>\n");
        # Fechamos a Página - Emitimos os comandos que finalizam a página em HTML
        $ano=date('Y');
        printf("<hr>\n");
        printf("<font size=2 color='gray'>Relat&oacute;rio de funcionários - Resolu&ccedil;&atilde;o m&iacute;nima de 1280x720 &copy; Copyright $ano, FATEC Ourinhos - Copie, divulgue, mas indique sempre quem fez! - medicosrel01.php</font>\n");
        break;
    } # 1.6.1.1
    #------------------------------------------------------------------------------------------------------------------------------------------------------------
    case ($passo==2 or $passo==3):
    { # 1.6.1.2 - pegando o valor da variavel $ordena do formulario anterior
        $ordem=$_POST['ordem'];
        $dtrefinicio= ISSET($_POST['anoini']).'-'.ISSET($_POST['mesini']).'-'.ISSET($_POST['diaini']);
        $dtrefinicio = ($dtrefinicio <> '--' && $dtrefinicio <> '1-1-1' ? $dtrefinicio : '2005-10-10');
        $dtreffim= ISSET($_POST['anofim']).'-'.ISSET($_POST['mesfim']).'-'.ISSET($_POST['diafim']);
        $dtreffim = ($dtreffim <> '--'  && $dtreffim <> '1-1-1' ? $dtreffim : '2012-10-10');
        # O proximo comando le a tabela de medicos ordenando os dados pela escolha indicada na variavel $ordem
        $sql = pg_query("SELECT f.*, count(s.*) as qtdsinistros 
                                    FROM funcionarios f RIGHT JOIN 
                                        (SELECT cefuncionario FROM veiculos v 	
                                            INNER JOIN ocorrencias o 
                                                ON v.cpveiculo = o.ceveiculo
                                            WHERE o.dtcadocorrencia BETWEEN '". $dtrefinicio ."' AND '". $dtreffim ."'
                                        ) as s 
                                            ON f.cpfuncionario = s.cefuncionario
                                    GROUP BY cpfuncionario
                            ORDER BY $ordem");
        printf("<table border=1>\n");
        printf("<tr bgcolor='lightblue'><td>Nome</td>
                                    <td>Sobrenome</td>
                                    <td>Sinistro(s)</td>
                                    <td>Data de Cadastro</td> </tr>\n");
        $cor="WHITE";
        echo $dtrefinicio . " FIM: " . $dtreffim;
        while ($le = pg_fetch_array($sql))
        { # 1.2.1 -----------------------------------------------------------------------------------------------------------------------------------
            $dtcad = explode("-",$le['dtcadfuncionario']);
            $dtcontr = explode("-",$le['dtcontratacao']);
            $dtnasc = explode("-",$le['dtnascimento']);
            printf("<tr bgcolor='$cor'>
                                  <td>$le[cpfuncionario] - $le[txprenomes]</td>
                                  <td>$le[txsobrenome]</td>
                                  <td>$le[qtdsinistros]</td>
                                  <td>$dtcad[2]/$dtcad[1]/$dtcad[0]</td> </tr>\n");
            $cor=( $cor == "WHITE" ) ? "LIGHTGREEN" : "WHITE";
        } # 1.2.1 -----------------------------------------------------------------------------------------------------------------------------------
        printf("</table>\n");
        if ( $passo==2 )
        { # 1.2.2 vamos montar o botÃ£o para impressÃ£o -----------------------------------------------------------------------------------------------
            printf("<form action='relatorio002.php' method='POST' target='_NEW'>\n");
            //printf("<input type='hidden' name='acao'  value='$acao'>\n");
            printf("<input type='hidden' name='passo' value='3'>\n");
            printf("<input type='hidden' name='ordem' value='$ordem'>\n");
            printf("<input type='hidden' name='salto' value='$salto'>\n");
            # montando os botÃµes do form com a funÃ§Ã£o botoes e os parÃ¢metros:
            # (PÃ¡gina,Menu,SaÃ­da,Reset,AÃ§Ã£o,$salto) TRUE | FALSE para os 4 parÃ¢metros esq-dir.
            botoes(TRUE,TRUE,TRUE,FALSE,"Gerar para Impress&atilde;o",$salto);
            printf("O mesmo relat&oacute;rio ser&aacute; montado em uma janela!<br>Depois voc&ecirc; pode escolher a impress&atilde;o pelo navegador.\n");
            printf("</form>\n");
        } # 1.2.2 -----------------------------------------------------------------------------------------------------------------------------------
        else
        { # 1.2.3 - O fluxo passa por aqui quando o $passo valer 3 ----------------------------------------------------------------------------------
            printf("<hr>\nDepois de Imprimir rasgue na linha acima<br>\n");
            printf("<input type='submit' value='Imprimir' onclick='javascript:window.print();'>");
            # Aqui montamos o final de pÃ¡gina quando o relatÃ³rio vai para a impressÃ£o ($passo valendo 3)
            $ano=date('Y');
            printf("</dir>\n <hr> \n");
            printf("<font size=2 color='gray'>&copy; Copyright $ano, FATEC Ourinhos - Copie, divulgue, mas indique sempre quem fez!\n</font>\n");
        } # 1.2.3 -----------------------------------------------------------------------------------------------------------------------------------
        break;
        break;
    } # 1.6.1.2
    #------------------------------------------------------------------------------------------------------------------------------------------------------------
} # 1.6.1
# o comando que emite as TAGs de fim de página acontecem SEMPRE (qualquer valor de $passo).
# Por isso o printf() está FORA do SWITCH-CASE
printf("</body>\n</html>\n");
?>