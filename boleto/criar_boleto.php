<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
    <center>    <label>Banco.: </label><select name="banco">
            <option value="boleto_bb.php">Banco do Brasil</option>
        </select>
        <form action="boleto_bb.php" method="POST">
        <label>Valor Cobrado.: </label><input type="text" name="valor_cobrado" value="" /><br />
        <label>Nome do Cliente.: </label><input type="text" name="sacado" value=""/><br />
        <label>Endereço.: </label><input type="text" name="endereco1" value=""/><br />
        <label>Cidade - Estado - CEP.: </label><input type="text" name="endereco2" value=""/><br />
        <label>Demonstrativo.: </label><textarea name="demonstrativo"></textarea><br />
        <label>Agencia.: </label><input type="text" name="agencia" value=""/><br />
        <label>Conta.: </label><input type="text" name="conta" value=""/><br />
        <label>Descrição do Boleto.: </label><input type="text" name="identificacao" value=""/><br />
        <label>CNPJ / CPF.: </label><input type="text" name="cpf_cnpj" value=""/><br />
        <label>Endereço.: </label><input type="text" name="endereco" value=""/><br />
        <label>Cidade - Estado.:</label><input type="text" name="cidade_uf" value=""/><br />
        <label>Razão Social / Nome.:</label><input type="text" name="cedente" value=""/><br />
        <input type="submit" value="Gerar Boleto" />
        </form>
    </body>
</html>
