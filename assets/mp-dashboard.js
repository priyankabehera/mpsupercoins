document.getElementById("mpwscoin_use").addEventListener("change", myFunction);

function myFunction() {
    var mpwscoinUseDom = document.getElementById("mpwscoin_use");
    var mpSalesPriceDom = document.getElementById("_sale_price");
    var mpSalesPriceVal = mpSalesPriceDom.value;
    var mpwscoinUseVal = mpwscoinUseDom.value;
    setSupercoinValue = admin_object.set_mpwscoin;
    var superUsepriceCal = 0;
    superUsepriceCal = mpSalesPriceVal - (mpwscoinUseVal*setSupercoinValue);
    document.getElementById("_bestprice").value = superUsepriceCal;


    //console.log('superUsepriceCal', superUsepriceCal);

}