function okaycms_start_begateway_payment_widget(e) {
  e.preventDefault();

  var params = {
    checkout_url: begateway_payment_form_container.getAttribute("data-checkout_url"),
    token: begateway_payment_form.begateway_payment_token.value,
  };
  if(typeof(BeGateway) === "function") {
    new BeGateway(params).createWidget();
    return false;
  }
  else{
    return true;
  }
};

window.addEventListener('load',function(event){
  okaycms_start_begateway_payment_widget(event);
},false);
