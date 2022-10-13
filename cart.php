<?php 
session_start();
include("connect.php");
require_once "./functions/database_functions.php";
require_once "./functions/cart_functions.php";
error_reporting(0);
if(session_id() == '') session_start();
if (isset($_SESSION['user_email']) == true) {
    //GET CURRENT VALUES FROM DATABASE (User_name)
    $user_email = $_SESSION['user_email'];
    $sql = "SELECT * FROM Users WHERE user_email='$user_email'";
    $query = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($query)) { 
	$current_username = $row['user_name'];
    }
} 
/*
if (isset($_SESSION['user_email']) == false) {
    header("location: index.php");
    exit();
} */
// bookid got from form post method, change this place later.
if(isset($_POST['bookisbn']))
{
    $bookid = $_POST['bookisbn'];
}
if(isset($bookid))
{
    // new iem selected
    if(!isset($_SESSION['cart']))
    {
        // $_SESSION['cart'] is associative array that bookisbn => qty
        $_SESSION['cart'] = array();
           $_SESSION['total_items'] = 0;
        $_SESSION['total_price'] = '0.00';
    }
    if(!isset($_SESSION['cart'][$bookid]))
    {
           $_SESSION['cart'][$bookid] = 1;
    } 

    elseif(isset($_POST['cart']))
    {
        $_SESSION['cart'][$bookid]++;
        unset($_POST);
    }
}
// if save change button is clicked , change the qty of each bookisbn
if(isset($_POST['save_change']))
{
    foreach($_SESSION['cart'] as $isbn =>$qty)
    {
        if($_POST[$isbn] == '0')
        {
            unset($_SESSION['cart']["$isbn"]);
        } 
        else 
        {
            $_SESSION['cart']["$isbn"] = $_POST["$isbn"];
        }
    }
}
$getTotalPrice = total_price($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>CodePen - Shopping cart-js</title>
  <link rel="stylesheet" href="./css/cart2.css">

</head>
<body>
<!-- partial:index.partial.html -->
<div class="content">
    <div class="cartTop">
        <div class="countBalance">
            <span style="color: #866BAF">Total Products:</span>
            <span><?php echo $getTotalPrice; ?></span>
        </div>
        <div class="addressDIV">
            <span><a href="index.php" style="color: #fff">Back</a></span>
        </div>
    </div>
 <div style="clear:both;"></div>
		
		<div class="cartMain">
        <form action="cart.php" method="post">
			<table id="cartTable">
				<thead>
					<tr>
						<th>
							<label for="fl select-all">
					<input type="checkbox" class="check-all check"/>
					<span><a href="javascript:void(0)" class="selallSPAN">&nbsp;Select</a></span>
							</label>
						</th>
						<th>Item</th>
						<th>Price</th>
						<th>Quantity</th>
						<th>SubTotal</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
                    <?php

	    	            // print out header here
	    	            $title = "Your shopping cart";

	    	            if(isset($_SESSION['cart']) && (array_count_values($_SESSION['cart'])))
			            {
		    	            $_SESSION['total_price'] = total_price($_SESSION['cart']);
		                    $_SESSION['total_items'] = total_items($_SESSION['cart']);
                        ?>
                        <?php
                            foreach($_SESSION['cart'] as $isbn => $qty){
                                $conn = db_connect();
                                $book = mysqli_fetch_assoc(getBookByIsbn($conn, $isbn));
								$formatToVND = $qty * $book['price'];
                        ?>
                        <tr>
                            <td class="checkbox">
                                <input type="checkbox" class="check-one check"/>
                            </td>
                            <td class="goods">
                                <img src="<?=$book['image']?>" alt=""/>
                                <span><a href="##" class="goodsTitle"><?=$book['name']?></a></span><br/>
                                <span><a href="##" class="sellerTitle"><?=$book['author']?></a></span>
                            </td>
                            <td class="price"><?=$book['price']?></td>
                            <td class="count">
                                <span class="reduce"></span>
                                <input type="text" class="count-input" value="<?php echo $qty; ?>" size="2" name="<?php echo $isbn; ?>"/>
                                <span class="add">+</span>
                            </td>
                            <td class="subtotal"><?=number_format($formatToVND);?></td>
                            <td class="opration">
                                <span class="deleteOne">Delete</span>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php
	                    } else 
		                {
		                    echo "<p class=\"text-warning\">Your cart is empty! Please make sure you add some books in it!</p>";
	                    }
	                    if(isset($conn)){ mysqli_close($conn); }
                    ?>
				</tbody>
			</table>
            <input type="submit" class="btSave" value="Save" name="save_change">
            <input type="button" class="btSave" value="Delete" id="multiDelete">
        </form>
		</div>
		<div class="cartFooter" id="cartFooter">
			<div class="selall fl">
				<label for="fl select-all">
					<input type="checkbox" class="check-all check"/>
					<span><a href="javascript:void(0)" class="selallSPAN">&nbsp;Select</a></span>
				</label>
			</div>
            <!--
			<a href="#" id="multiDelete" class="fl delete">MultiDelete</a>
			<a href="#" id="allDelete" class="fl delete" onclick="return saveChange()">AllDelete</a>
                    -->
			<div class="fr closing"><a href="checkout.php">Checkout</a></div>
			<div class="fr total" style="color: #866BAF; font-weight: bold">Total: <span id="priceTotal">0.00</span></div>
			<div class="fr selected" id="selected">
                Selected product:
				<span id="selectedTotal">0</span>
				<span class="arrow up">⇧View Selected⇧</span>
				<span class="arrow down">⇩Cancel⇩</span>
			</div>
			<div class="selected-view">
		        <div id="selectedViewList" class="clearfix">
		            <!-- <div><img src="images/1.jpg"/>
		            	<span class="selCount">1</span>
		            	<span class="del">AAAAAA/span>
		            </div> -->
		        </div>
		        <span class="arrow">◆<span>◆</span></span>
    		</div>
		</div>
	</div>
<!-- partial -->
  <script  src="./js/cart2.js"></script>

</body>
</html>
<script>
window.onload=function(){
	//兼容document.getElementsByClassName 方法；
	if(!document.getElementsByClassName){
		document.getElementsByClassName=function(cls){
			var ret=[];
			var els=document.getElementsByTagName('*');
			for (var i = 0; i < els.length; i++) {
				if(els[i].className===cls
				 || els[i].className.indexOf(cls +' ')>=0
				  || els[i].className.indexOf(' '+cls)>=0 
				  || els[i].className.indexOf(' '+cls +' ')>=0){
					ret.push(els[i]);
				}
			};
			return ret;
		}
	}
	var getPHPTotal='<?php echo $getTotalPrice;?>';
    var config = { style: 'currency', currency: 'VND', maximumFractionDigits: 9}
    var formated = new Intl.NumberFormat('vi-VN', config).format(getPHPTotal);

	var cartTable=document.getElementById('cartTable');
	var tr=cartTable.children[1].rows; //children子节点;
	var checkInputs=document.getElementsByClassName('check');
	var checkAllInput=document.getElementsByClassName('check-all');
	var selectedTotal=document.getElementById('selectedTotal');
	var priceTotal=document.getElementById('priceTotal');
	var selected=document.getElementById('selected');
	var cartFooter=document.getElementById('cartFooter');
	var selectedViewList=document.getElementById('selectedViewList');
	var multiDelete=document.getElementById('multiDelete');
	var allDelete=document.getElementById('allDelete');
	var selallSPAN=document.getElementsByClassName('selallSPAN');

	//选择框事件；
	for (var i = 0; i < checkInputs.length; i++) {
		checkInputs[i].onclick=function(){
			if (this.className==='check-all check') { //全选；
				for(var j=0;j<checkInputs.length;j++){
					checkInputs[j].checked=this.checked;
				}
			};
			if(this.checked==false){
				for(var k=0;k<checkAllInput.length;k++){
					checkAllInput[k].checked=false;
				}
			}
			getTotal();
		}
	};
	selallSPAN[0].onclick=selallSPAN[1].onclick=function(){
		for(var k=0;k<checkAllInput.length;k++){
			if(checkAllInput[k].checked){
				checkAllInput[k].checked=false;
				
			}else{
				checkAllInput[k].checked=true;
			}
		}
		for(var j=0;j<checkInputs.length;j++){
			checkInputs[j].checked=checkAllInput[0].checked;
		}
		getTotal();
	}

	//计算；
	function getTotal(){
		var selected=0;
		var price=0;
		var HTMLstr='';
		for (var i = 0; i < tr.length; i++) {
			var perCount=tr[i].getElementsByTagName('input')[1].value;
			if(tr[i].getElementsByTagName('input')[0].checked){
				tr[i].className="on";
				selected+=parseInt( tr[i].getElementsByTagName('input')[1].value);
				price+=parseFloat( tr[i].cells[4].innerHTML );
				HTMLstr+='<div><img src="'+tr[i].getElementsByTagName('img')[0].src+'"/><span class="selCount">'+perCount+'</span><span class="del" index="'+i+'">Remove</span></div>'
			}else{
				tr[i].className=" ";
			}
		};
		selectedTotal.innerHTML=selected;
		if (price == 0){
			priceTotal.innerHTML=price.toFixed(2);
		} else {
			priceTotal.innerHTML=formated;
		}
		selectedViewList.innerHTML=HTMLstr;
		//选中0时；
		if(selected==0){
			cartFooter.className="cartFooter";
		}
	}

	//已选框的显示与隐藏；
	selected.onclick=function(){
		if(cartFooter.className=='cartFooter'){
			if(selectedTotal.innerHTML!=0){
				cartFooter.className='cartFooter show';
			}
		}else{
			cartFooter.className="cartFooter";
		}
	}

	//取消选择---》事件代理
	selectedViewList.onclick=function(e){
		e=e||window.event;
		var el=e.srcElement;
		if(el.className=="del"){
			var index=el.getAttribute('index');
			var input=tr[index].getElementsByTagName('input')[0];
			input.checked=false;
			input.onclick();
		}
	}

	//加减事件；
	for (var i = 0; i < tr.length; i++) {
		//加减按钮；
		tr[i].onclick=function(e){
			e=e||window.event;
    		document.onselectstart=new Function("event.returnValue=false;");
			var el=e.target||e.srcElement;
			var cls=el.className;
			var input=this.getElementsByTagName('input')[1];
			var val=parseInt(input.value);
			var reduce=this.getElementsByTagName('span')[3];
			switch(cls){
				case 'add':
				     input.value=val+1;
				     reduce.innerHTML='-';
				     getSubtotal(this);
				     break;
				case 'reduce':
				     if(val>1){
				     	input.value=val-1;
				     	getSubtotal(this);
				     }
				     if(input.value<=1){
				     	reduce.innerHTML='';
				     }
				     break;
				case 'deleteOne':
				//单行删除；
				     var conf=confirm('Delete it?');
				     if(conf){
				     	this.parentNode.removeChild(this);
				     }
				     break;
				default:
				     break;
			}
			getTotal();
		}
		//input输入事件；
		tr[i].getElementsByTagName('input')[1].onkeyup=function(){
			var val=parseInt(this.value);
			var tr=this.parentNode.parentNode;
			var reduce=tr.getElementsByTagName('span')[3];
			if(isNaN(val)||val<1){
				val=1;
			}
			this.value=val; //输入控制法；
			if(val<=1){
				reduce.innerHTML="";
			}else{
				reduce.innerHTML="-";
			}
			getSubtotal(tr);
			getTotal();
		}
	};
	//小计
	function getSubtotal(tr){
		var tds=tr.cells;
		var price=parseFloat(tds[2].innerHTML);
		var count=tr.getElementsByTagName('input')[1].value;
		var subTotal=parseFloat(price*count).toLocaleString('vi', {style : 'currency', currency : 'VND'});;
		tds[4].innerHTML=subTotal;
	}

	//删除；
	multiDelete.onclick=function(){
		if(selectedTotal.innerHTML!='0'){
			var conf=confirm('Delete it？');
			if(conf){
				cartDel();
				getTotal();
			}
		}
	}
	allDelete.onclick=function(){
		var conf=confirm('确定清空购物车吗？');
		if(conf){
			checkAllInput[0].checked=true;
			checkAllInput[0].onclick();
			cartDel();
			getTotal();
		}
	}
	function cartDel(){
		for (var i = 0; i < tr.length; i++) {
			var input=tr[i].getElementsByTagName('input')[0];
			if (input.checked) {
				tr[i].parentNode.removeChild(tr[i]);
				i--; //删除时注意i 的变化；
			};
		};
	}
}
</script>