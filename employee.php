<!DOCTYPE html>
<html>
    <head>
        <title>Formulario</title>
        <link rel="stylesheet" href="css.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <?php 
        	require 'conexion.php';
        	//SI ALGUIEN INTENTA ACCEDER A LA PAGINA employee.php Y NO EST� LOGGEADO, LO REDIRECCIONAR� A LA PAG PRINCIPAL
        	if(!isset($_SESSION["user"])){
				header("Location: ./index.php");}
			//$ROW PARA FINES PRACTICOS
        	$row = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `users` WHERE user='{$_SESSION['user']}'"));
        	$date = date('Y-m-d');
        	//FUNCION PARA DEPOSITAR Y OTORGAR PRESTAMOS
        	if(isset($_POST["deposit"])){
        		$user = mysqli_fetch_assoc(mysqli_query($link, "SELECT `amount` FROM `USERS` WHERE account={$_POST['account']}"));
        		mysqli_query($link, "UPDATE `USERS` SET amount = (amount+{$_POST['amount']}) WHERE account={$_POST['account']}");
        		mysqli_query($link, "INSERT INTO `{$_POST['account']}` (date, description, amount, total) VALUES ('{$date}', 'Deposit effectued', {$_POST['amount']}, {$user['amount']})");
        	}
        	if(isset($_POST["loan"]) && ($row["amount"] > $_POST["amount"])){
        		$user = mysqli_fetch_assoc(mysqli_query($link, "SELECT `amount` FROM `USERS` WHERE account={$_POST['account']}"));
        		mysqli_query($link, "UPDATE `USERS` SET amount = (amount-{$_POST['amount']}) WHERE user='{$_SESSION['user']}'");
        		mysqli_query($link, "UPDATE `USERS` SET amount = (amount+{$_POST['amount']}) WHERE account={$_POST['account']}");
        		mysqli_query($link, "INSERT INTO `{$_POST['account']}` (date, description, amount, total) VALUES ('{$date}', 'Loan effectued', {$_POST['amount']}, {$user['amount']})");
        	}
        	//PARA REGISTRAR NUEVOS USUARIOS (CLIENTE/EMPLEADO)		AQUI ES DONDE ESTOY TENIENDO PROBLEMAS
        	if(isset($_POST["signup"])){
        		if (preg_match("/^[a-zA-Z]*$/",$_POST["user"]) && ($_POST["user"]!="" && $_POST["pass"]!="" && $_POST["pass2"]!="")){
        			if (mysqli_num_rows(mysqli_query($link, "SELECT `user` FROM `USERS` WHERE user='{$_POST['user']}'"))==0) {
        				mysqli_query($link, "INSERT INTO `USERS` ( user, pass, names, lnames, birth, email, tel, cel, address, school, area, type, employee) VALUES ('{$_POST['user']}', '{$_POST['pass']}', '{$_POST['names']}', '{$_POST['lnames']}', '{$_POST['birth']}', '{$_POST['email']}', '{$_POST['tel']}', '{$_POST['cel']}', '{$_POST['address']}', '{$_POST['school']}', '{$_POST['area']}', '{$_POST['type']}', '{$_SESSION['user']}')");
        				$id = mysqli_insert_id($link);
        				mysqli_query($link, "CREATE TABLE `{$id}` (date VARCHAR(10), description VARCHAR(60), amount DECIMAL(16,2), total DECIMAL(16,2))");
        				echo "<script language='javascript'>alert('The account is: " . str_pad($id, 6, '0', STR_PAD_LEFT) . "');</script>";
        			}
        			else {
        				echo "<script language='javascript'>alert('Signup no valid: The username already exist');</script>";
        			}
        		}
        		else {
        			echo "<script language='javascript'>alert('Signup no valid: Please checkout user/password fields (only words)');</script>";
        		}
			}
        ?>
		<script>
            $(document).ready(function(){
                $('#table tbody tr').click(function(){
                    window.location = $(this).attr('href');
                });
            });
        </script>
    </head>
    <body>
    <!--------------------------------------ASIDE------------------------------------------>
		<a id="wrapper" class="ancre"></a>
    	<a id="close" class="ancre"></a>
  		<a href="#wrapper" id="hamburger"><div></div></a>
		<a href="#close" id="close"></a>
		<div id="wrapper">
    		<div id="image" style="text-align: center;">
    			<a href="."><img src="logo.png"></a>
    		</div>
    		<div id="right-ul">
      			<ul>
        			<li><a href="#home" style="font-size: 1.5em;"><img src="data:image/png;base64,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"/>Home</a></li>
        			<li><a href="#signup" style="font-size: 1.5em;"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFIklEQVRYR6VXS24bRxCtGtKCd+EyWYnZGUgkTE6QWWVr5gQmEBkwsiKVC/AEpLILQAbgEXgE5gQeSw6QJXMC0zvDIqfyXvU0NRoyHCqZDTmf7q5+9d6rapXadfXtJNPEembSEdFzvM5FZc3P1KRrIt04RMXwCO9U3+L/WkzyQqWrortvqtNjzne/3w0X1We6F8DFeG6iC8y8lkTScuF1616WmzPp7b43S2d3133ev74YLxHeBwSDoO2jj8F/Dy4EmEuBjSSSzW6Ho6MBcDJTDVGGQamYZaqyKkyX2Gmfk2OJ+fRuOPcAvpmkpniGb9UMQeqc99yECe4RAP6vAGE6vb0enBQAFgKM+j1gxcAIvX3UrfYksRsEiV2Xl+HbcpeqNsL/cwRNJBBASF9EZXp3nR1PweV4ZaYDBNCLEMcBP11Meo4A0KjupJ9OOs+2tsBiq/a9jn77a7iKY8gpQu+pEBvMbq+xsYdrnwMIQArtA65+PYDqwFP/O6kdFaTTJAMCzqsKKg83b15Muttn2InqnCmo7jLupE6ipkCcH4mnJQcC/UYEQMLcSRhY7qyPEMf8XUEph6B25VSu6Z9DLCpydTkZMQUgaP++pdk8H1Z58XgPV0iBmo7IXgbAvCdgfYEJqOESJUgVE1Jq+Hb2frgM42RV+kKuW1nEAFxZ+A7knZ+EANh/g7BeQmY/Buhtvkk0ZeRE4/kn6VSJFlGq7qy6rdInIE0bnRQAVVAlIVGoO1hT7uP7KgeA2Mvp7fC7ZhUAAearztj/EogjCEWBhJDmaSSMTtipMp5EgpfnRIK7ivnlbsgL/m7PbFB3umoAdMmjMuQkzFcROCC+2OX4Rraw1gQmAu+PUoWul6hFX+A3NbO/g91ahjrylsUrFp4oX7diGFFjAJQYqhlQkC4R8AXPLG991pTEi74PN+xSGfwF45fUuhY6KhLcQ46btuQk5c4JC1nCkG4aA/Adm+ZRdq5j5JFSO5V41e9ieeczSLTXqAIiEFxLBAh4KqLMWlvJnqoGBmBb+xL14KukJeeN1ZCGUvrAYxJejBdwv0Fd/5GAn57L+pAPlCkATwRGVfw8e//LD40ypBPG2s2cs86zvhMRv29BVkjTfUsW7UJYOeGI7IRYtr0x+TUR+wDbfdfeoEdgOff+wFWQHQ2gbEjYeHQIF+Q3qA70kuzMl3zbkiVdkZ0SiYgUrfGsQ8UwjZtE5kwbA+Cih7xlrxyzGEGGo0Qti/l6fTGBKYXu56nXQyEK/UCzClgNacUH+renLu4KCpWQ7d3y1GIUKpe68Tw0oZiDhKuTsCkoDwAtG9IABJS14OsmDjgC7GJImLgo08CB7I6ZZ+abzPccs/kEyZIC5Ri/NCF+60YUAkjdKUVenRRAkKEXo4wTVDtakpJdMuZnP5CyD6y24FQC3qFrlnUcXwZAYj/qspyYdQhLH5gTNno/63+7MDA69AP8ns/4n+gQhTjHQR8oOUDVQJokIYPfXfsqgBVDy2yf9ipXU74PvY9GRFSitI8GQCv2kxEKR923/0cAu12fdDJiOabZlA7XUdVXu8XN/vAWu7wcLZgWb+mGLM+RlLFJjV023bPuJ/+WglVcwCfBEc1aktHP+dzZXt6XLueki+89eDaoLNXeqBrd0492jYfT3dkwSAcMd59H/sJRDJOhxSoPm2i1PF1hARzDyJ0gRTIe9xzPX5yc/fi2OikFseWuUBXe4PLiSXcXFInlzx4uT0Us5zsUmSJjQdNlPYB/AJ18aE4kNt/BAAAAAElFTkSuQmCC"/>Sign-up</a></li>
        			<li><a href="#mortgages" style="font-size: 1.5em;"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFAklEQVRYR51XQW7bVhR8j5KD7KobRN4VaJ0oJ4h8gqgnsIDaQHcVfYGyF7CYXQApKH2CKCcIjyBYLdCddYOquyA2+TvzyC+wMkVR+SvJpv6fN2/ezKfKEevy5XQizg1EZShOliJ6r+oUW9w/BHqbLMPNEdvZo/xxqzUeTHsnmVvg4Z5TXQBIn5+xQR+7pNjqrWTy0+yvEMDar9YAuOXPZ9NRkMvaBW6iot9JLr+7jgxVDIzkTtMPq5AgW69GAL98P+1nJ/KbE9CsutZMFqzQWoHq53dhhM+ROnnhnNyiHRGYSGarMGmLYC+Aqx+mA1bafdDo/d/humnDq7PpWMRN0JoErQEjumgLohYA+93NXDJfXY+qB/PvFBqZ+fJcNruiuzy7ScgUhdoWRC0AUtz9KgtfOdmQwMUQ22B2d927Oruh0KD4p3QThCr+52TQedDxIfb2AYjYX1a/Vb/K0omMul91mJ04VIrvTl/NV+H51cub2Ilu+Bv/PNqRKtoxW10Pm9p3EIAXXEGr9HHohGKzA0Tezu7C1wCwAYAYYlyz95c/ToeibkLGNNNR02jWAmBFoJpKF1C6QF9ZzciqznUBin/Fvz5BeGNWWLREYwoR3wf8Hf6W0i84on6vOiYOM8Ce5hpjIiI83AMDkQZuhIrX+H5hDBSHpQQ0v7s2T9i6Jj5DzJiS+rWXAVhrRJVzxOACfVSCw6UHS4DIrAULsEIGBmSMJhSofAQg27Nsw5i27UG1ZoBj9vhMUE0hxHK87nHguTGgNhEplc4WsE1gJAHIsbGk+hk/+9c8QfUVQJ0exUDRQ6ucbofe4jtHEYuC2lKOPCC9NnqiaIP0CdosW92QbXoMJGkKqUYrNhAC8Yn8gYMgumKZRT+Dyp0uTfXl7PtR3Fdt6xZUH7S5zuHxoBu0f6yL3ZIBSMSiGUv/0Vxu2yTjUWlowgqQfggl6bg3GK93HmzRMgujGEgwIYUecnxvSsjmFlT6vlV2ACZyXEYCuKHTezohgXUfZU3bLdLRDWnT1FAhRBhU6Su7bXgCoEhBuUAFr/kw7HfjNzAGuMCCd0ZUGAVMQhGEkCbzP8PUpuiEkwJhko1ijGsN6QkAGggf5sxDVHQ9qt+uWrDaDTf0AHCA3X7oERQkjagaQJYRpXfYb3JJCbDKQh0ACyJW6z2fAGjF5v/oP25BI9JcuB9pcn1LwAz3gQ7MqUK3j2gAWbIY7y0exNMWwFarCUaTYbazOh7ob0F2J2T2F+D4vwTgUgLwLspD/N0CDMVk4SAAGyl4vx8h0ljSy3DamPNRaLRmRDBpRW9guYp4Rr/ZJoivqvzy2rbGc7xPWMjtZcB7uA+Q4l7ooGhLtogXEm7oe2/pWIaTTUcp0mqlfnzJ2m4w7Q0jbuwFU975LJQYLFsA1AGywQTGRTYwpmTifwxQTwDJRw4y4Kmx3uca+VaU/p6gv/1uLhOKCn6fAOjIb26Kr0m/0htGkut41x0bjaislFctCyR/KfUTQsvFoe8MgMN7Ar1j5xATYS5IUgnrbsoHrdhrAvR/zgL9VE02spJ1JH3+BZVj7V5Ay6s9ckTj3fnfK8KqQquf/T3vsaPjpngtx+4NZv6cU4I4jr85jnfBWEsoNBsBxDHGEoAmZAAXmAuo/BSUDmjPbV/RDragCsK/Ldl7obhTe0ktl7+yEdAxb8lHAdhOCMZqO6L2TiAQoaTHHs79/gMvaEROz6IeXwAAAABJRU5ErkJggg=="/>Mortgages</a></li>
        			<li><a href="#clients" style="font-size: 1.5em;"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAEaElEQVRYR81XUXLiRhDtlrB/zZ7A7F+qEmz2BMYnWHyCQMVU5c/IFwh7AST/pQpSYU+w5ARLTrBgNlX5M3uCZX9XSL2vRxosFAQGVyWZDxtJM5rX3e+9aTHlxnW1N2Lmc9yu6CMhWuDPhPFfmCZ6j0nMPWGur5aLmDl6Hbo8HU4883vXwJrH0T7rBdiwwUJzYr7Ytbj4udz17287T1m/DqDa06iGg3svsIuvf/Afo0xvIhNlZqoVbYCX/iJCwWDmebtArAG4PuvNNbWD2W1j18Jtz9tVv4k6/Y45E4qo1f/LM6XbNHIl8IVE/uzPbv8R9b6A2t/7NXJlLMRImHRRkrt/FYBu1qz55aNIxuDTOTI7Cl1q5cm5NQPXZ34HhDwHMSsFGZgg1Qukbe5EPN2U6hTEKCX1JHToMgtiK4D2GUqyx7CSLYXU+vVvb55dCnkPIe8flRf9e++VffYEAHIXOtzVVEIh75YO3ZViQmakERN3kaEFytzBi8p4Psb9OqI92US+drWn5bgAgNW+hQB+/s6vRMf0ACv6QsKd/swbJiWRZszkc8yfmCXIgGoiqjIABinYU47oMluWvQCo/tmh9yBPCzWuM/GJG5LRdXQkQ8PumDxxpAMe1AUgNRsA1UVmAgfMT8nXUvC6LgFANSiivLMEjwBk6obciI4IqUXExL6xYmySpwfMZ6ygyDHzVH4vMGdhZQ0A6gera11fWAILIBa6QjQdpPp96Su9XR7LGNk4FZG32GRkonZEjQsl4BNDRKIAYKZaIsz9bElnSJ3zmUIA1s0UwG8zb5RcSxJ1xPW85IzcYpkrAENI4Q8AMAWoG0u6vQCAcF3j6URvgLqGaIcO0zsLaJM6DUmJfCjgFcowNBzAeBYAu1gBQRHNwf1tkSmRVY4eREuX3pQiUe2/fhYA1PqPpcvNVPv1befE6hCCchSs8QNS1nucIfXaWVNMQlMCSAz1TlhteoTX0PnLomZj5XYogXIkbW5MBiwADSh72m4FAPQ3FNPVskQTm061UnU5N6JF1m5t9ClnKgD7oEpQ3qwBAKfQb6CcydieAditiZz4wdQ0Vqmp7SYyRCS4hsGgk1K2628cNi80QwpIYEo69zkATM0TdsPZ4HbaaACUl+2a1GDgE6fJHLqCBLuDj97YKukwANqKqZTgbqWQTcpgwSNsrh2TiTw7fqr6DWO/yBbcBpllnCNo21heqnJWHHhqCbSjEVdGS4drMJi0ZnwjMV1qdHkAep2YEX02PuBCBSiLLdXeAJLa+uARdU3DIdxA+pUTa+nPA0lPPLXkinHEFPBBANK0at1tBzx0v3KQbzayIGzds8e4Pj8IwKY0H3rvPweQdMn0QX3iST5waKTb1qWc+l8D6C20q4HuO+GxXMDFKpsiQl+4kiG6pU/bSJldvzMD9vB4Tvo1gKL1+J5E7/ho4Tpv7Sww5zkaTtydxKItNi30IDLersaEj9LVyx0jTXMNCy7DMdc/Vjd9XYvpkALbpOrab2GXgk7O76JLAAAAAElFTkSuQmCC"/>Clients</a></li>
        			<li><a href="./index.php?logout=Logout" style="font-size: 1.5em;"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFPUlEQVRYR71XQXIaVxDtnkGq7IxOYLLLIrLQCYJPEPkERhW5KjsDFzC6gBh2qZJcGp1A+ATCJwBLSVV2Qicw2rkMTPu9nhnAWEgjp5ypQhrgz+/Xr1+//mi92imXErlWkbLgMpEx/gx575dKP781sVFgMjLV2vz7lRs164cTvfnr3+Zo3Zrlz/XVdqeOjfcAoGKmjfmXgVRx76Cw6dqAdwThM+Pjq1ahZ/Rg+yhmcISJESnCw2+OL1vdIujvWnPwa6emgXSOL5u7RfYAA0f9RDQKmK1aFfRXRfWJJdI8+bs5p7/IZlyTAbgAAOT18KWvnnUM15mYxqWpjKYb1gYbT1VtFyUZANDhY4B8H4BEnnutVSqTUHrhTGoqVvfSqO6YQYgFgXwfADCgiUbH/zSHB886DQQczULpbyRkQ+opkfoEIHoAuB8Pm+N15K4CoMjDifTXdUVaAjKQql5OLpvRn790KtNNqStbDi+UIwITLNWNs5JonWDXiVACi08uW5UcjJh9QAIDADlcBeIA0P3d8LNGyLpsgTVM5eLtZeuMG0CYdQSHT9gWGMB7ALlHoPSVjZnsHV814wUA2ceeFYB/g2DtaSDdnEUAOBonpk5zoFZLRAYBnAagEFjOJ4GecUNTa6voR2wABuRFLsw0oO3gc3SROov5BQYreO65mrbZYZZoL2OTXbbPPbwNEWhoomMgi9AJaEODIek1Pr9NTQj3iXSTAFmwHChBDgCaaWPN3iIqAMJJPWN3Tc+858EDX3eN18/Y83Vi8sIBIOGe1xqOuEw/grUBbIDs3vO7rCue5ui97wEA/yhKd0CsAwBd6COBrYcGt9URAI3BMNewrEPs9zIFAIrcAyg8sTLAcJOIWf6x3dkLxL0hYouWEht+y4DsgKFD6gfFG+N5t/A0iFVQxrZ3FEwOyYxKE23PNrGPSLwoASiaB4QWlhd/+knG3IDAVPV3ds3XJQAAXJgpMaISBJMY01fILL65nYZax9BDZyH77JqEWvNZAKQjnaFOoeyVPkvMgFjcyIZQGSU6p3KpD/j8xTIAZwhizQDcsO55ADzXp/KBLAa4LQcxsxjAaui6KlsyBZAquI8gMQNzAwpyY4IMAjcj1E0qicppgKG1DGAuvjU33uaGNqSG4KpgY0tmWst9JNWAnwE0ghnVvHRkA4GBFEOl1XWhmYF+ik1f3wcgNzEYGoFLOmsERgZHNXtPFpad9CsNEIC3TtoN5wB1CwHCmhWthuCBO+Jv9wHIzWcSyBYDgeGeixFJ3eWeCwawCEiHyLKadUIZtMMT5BQtBBe0KtTbmG3K9X0A3JgS+Vi0TLkP9EkZBeVeIHQsAKEJ4WyA4DAzBSO2u9oFd5U+ny9FxvhchLBZTrqe05bWHD2rVCyB0MFSs+F4XmrD/w4gdTJXPfuU92jFXt6K3wApCKDwiSjz8hp7lmXITskN1wHMaXFK4gFZOTdOi4iwMAAeGNCbHD7wfNY6HcU5EDLAFs2ARA9p4NEnIn8AQ8dPxRgSHEysOQ+qb6+avVTVPpTG8Pu+O+Hqb4clIdCG8bZamAE+y16l6cAe3+FQGlGQYKKM7GlMpydXrXcZUPqAi3BakqGP7vzKTIxv2c4E/5BLuunli/z3Qbp5049nad+3/ZQM7+YEdDMKZVA0u0cBcCZwIIX40IJ6jizOeDDJmZltGAaKDvC+8cMAEIR7ObIGGy99iql8IBs8NbMs1MAPBTAvSXYgzYD00CG3Prb9p1uxXz2PLsFdD6wwgsGCE+//CSAHlQOhIHnmL5JdkTVfAN/Yg55Ir24xAAAAAElFTkSuQmCC"/>Logout</a></li>
      			</ul>
    		</div>
  		</div>
    <!--------------------------------------HEADER------------------------------------------>
		<nav align="right" style="text-align: right;">
			<img id="search" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAB00lEQVQ4T5VTQW6jQBDswvZqjzzBx0jZJDyBJ/AEDra0R/AL/ILA3iLZK/GD+Ak8AcWJtEeeQG5RbOitnhgHW1ak9GkYuouqrgIyqNmvLBRPQoj6quJDJFDgce/Jn6JKm2Fvf2bPR81v73MV1BysPREDqBUEgUxFNUSH5eolrc5BHMDsNluCA3Z2Qyp3KhyElLx6JaOpMdqPkJwzgdGGp5F22Ag0FqAev0vx9lOaSSuRika8q8hiSjbN6mmRDFnAqHeK0lFlrZ/SfNgQB5k/brWA8AOi8Wq7CC8D8OV6u4guLcokHu87KdfPqUlzhdnNvaGXpB+c0+ubCGC0/cNzM2TpJEiLQj1NyCC+xKCX6UFDk/t3m1LOJ4MNV5/T/+CrHdDiwgBG78gf/qXOMSdhfpPFB+sYFE0YnA2Dk5tdtsBJqxtVLF0P9zTeITkBMBRbEn2u6H/jLFXLAPjMM1BIJ9UhVAHtDIahOibRFuUCI2gMzKWR1hIkZKhqy4eFzYCMKVpElswjgDH5fZVN9z/ErPTtSxwqKaeY7MjI07wT5MxLQEbMjeajHaITgEsO9Hfz6ywwp1wqrSjr48f7RhmIjBh3RdWJNJ5J+ca8a3XR7iTpF/wffLL5YpgtBXIAAAAASUVORK5CYII="/>
			<input type="search" placeholder="Search..." />
            Welcome <b><?php echo $_SESSION['user'];?></b>
  		</nav>
    <!--------------------------------------CONTENT------------------------------------------>
		<div class="cont" id="cont">
  				<div id="home">
  					<h3>HOME</h3>
					��������������Today:<b><?php echo date('l jS \of F Y');?></b>
					<div>
						<!-- BLOQUE DE CAMBIO DE MONEDA, COMO ES MUY LARGO Y SE REPITE LO PUSE EN UNA FUNCION -->
						<?php currency(); ?>
						<!-- GENERAR PRESTAMO -->
						<div class="block" style="width: 26%; float: right;">
							<b>Loan</b><br><br>
							<form method="POST">
								<input type="number" name="account" placeholder="Number of account"><br>
								<input type="number" name="amount" placeholder="Amount" min="0" step="any"><br><br>
								<input type="submit" name="loan" value="Loan">
							</form>
						</div>
						<!-- GENERAR DEPOSITO -->
						<div class="block" style="width: 26%; float: right;">
							<b>Deposit</b><br><br>
							<form method="POST">
								<input type="number" name="account" placeholder="Number of account"><br>
								<input type="number" name="amount" placeholder="Amount" min="0" step="any"><br><br>
								<input type="submit" name="deposit" value="Deposit">
							</form>
						</div>
						<div class="block" style="width: 94%; float: left;">
							Your number of account is <b><?php echo str_pad($row["account"], 6, "0", STR_PAD_LEFT);?></b> and you have $<b><?php echo $row["amount"];?> USD</b><br>
						</div>
					</div>
  				</div>
  				<!-- REGISTRAR USUARIOS -->
  				<div id="signup">
  					<h3>SIGN-UP</h3>
  					<form method="POST">
						<h3>Personal data</h3>
  						<input type="text" style="width: 32%" name="names" placeholder="Name (s)" value="">
						<input type="text" style="width: 32%" name="lnames" placeholder="Last names" value="">
						<input type="text" style="width: 18%" name="birth" placeholder="Date" onfocus="(this.type='date')" onblur="this.type='text'; this.placeholder=this.value;"><br>
						<input type="email" style="width: 32%" name="email" placeholder="E-mail" value="">
						<input type="tel" style="width: 25%" name="tel" placeholder="Phone"  value="">
						<input type="tel" style="width: 25%" name="cel" placeholder="Celphone"  value=""><br>
						<input type="text" style="width: 89.6%" name="address" placeholder="Address" value=""><br>
						<input type="text" style="width: 43%" name="school" placeholder="School" value="">
						<input type="text" style="width: 43%" name="area" placeholder="Area" value=""><br><br>
						<h3>Account settings</h3>
  						<input type="text" style="width: 22%" name="user" placeholder="User" value="">
						<input type="password" style="width: 22%" name="pass" placeholder="Password" value="">
						<input type="password" style="width: 22%" name="pass2" placeholder="Confirm password" value="">
						<div id="radio" style="display: inline; width: 22%;">
							<input id="customer" type="radio" name="type" value="customer" checked="checked"><label for="customer">Customer</label>
							<input id="employee" type="radio" name="type" value="employee"><label for="employee">Employee</label>
						</div><br><br>
						<input type="submit" style="width: 18%; float: right;" name="signup" value="Sign-up">
					</form>
  				</div>
  				<!-- HIPOTECAS (PENDIENTE) -->
  				<div id="mortgages">
  					<h3>MORTGAGES</h3>
  					<p>Here is the mortgages...</p>
  				</div>
  				<!-- VISUALIZAR A LOS CLIENTES DEL PROPIO USUARIO -->
  				<div id="clients">
					<?php if ($_SESSION['user'] == "admin") { ?>
						<h3>EMPLOYEES</h3>
  						<table id="table">
							<thead height="50%"><tr>
							    <th># Account</th>
								<th>User</th>
								<th>Amount</th>
							</tr></thead>
							<tbody><?php
								$query=mysqli_query($link, "SELECT `account`, `user`, `amount` FROM `USERS` WHERE type='employee'"); //WHERE type='customer'
                    			while($row = mysqli_fetch_assoc($query)) { 
                    				echo "<tr href='?account={$row['account']}#visualizar'>
                    						<td>{$row['account']}</td>
                    						<td>{$row['user']}</td>
                    						<td>{$row['amount']}</td>
                    					</tr>";}
							?></tbody>
						</table>
					<?php }	?>
					
  					<h3>CUSTOMERS</h3>
  					<table id="table">
						<thead height="50%"><tr>
						        <th># Account</th>
								<th>Names</th>
						        <th>Last name</th>
								<th>Amount</th>
						</tr></thead>
						<tbody>
							<?php
								if($_SESSION['user'] != "admin"){
									$query=mysqli_query($link, "SELECT `account`, `names`, `lnames`, `amount` FROM `USERS` WHERE employee='{$_SESSION['user']}'");}
								else {
									$query=mysqli_query($link, "SELECT `account`, `names`, `lnames`, `amount` FROM `USERS` WHERE type='customer'");} //WHERE type='customer'
                    			while($row = mysqli_fetch_assoc($query)) { 
                    				echo "<tr href='?account={$row['account']}#visualizar'>
                    					<td>{$row['account']}</td>
                    					<td>{$row['names']}</td>
                    					<td>{$row['lnames']}</td>
                    					<td>{$row['amount']}</td>
                    				</tr>";
                    			}
							?>
						</tbody>
					</table>
				</div>
  				<div id="visualizar">
  					<?php $report = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `users` WHERE account='{$_GET['account']}'")); ?>
  					<h3>FINANCIAL REPORT</h3>
					<b style="float: right;"><?php echo "Date: " . date('Y-m-d') . "<br>Time:�����" . date('H:i:s'); ?>
					<br><br>Branch office: On-line<br>
					Tel: (449) 910-8417<br>
					Email: services@banuaa.net
					</b>
					<h4>
						<br>Acount number: <b><?php echo str_pad($report['account'], 6, "0", STR_PAD_LEFT); ?></b>
						<br>Type: <b><?php echo $report['type']; ?></b><br>
						<br>Name: <b><?php echo $report['names'] . " " . $report['lnames']; ?></b>
						<br>Amount: <b><?php echo $report['amount']; ?> USD</b>
					</h4>
  				</div>
		</div>
    <!--------------------------------------FOOTER------------------------------------------>
		<?php footer(); ?>
    </body>
</html>