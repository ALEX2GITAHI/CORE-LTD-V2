// cart.js - simple cart using localStorage, reads from data/items.json for details
function getCart(){ return JSON.parse(localStorage.getItem('core_cart')||'[]'); }
function saveCart(c){ localStorage.setItem('core_cart', JSON.stringify(c)); }

async function addToCart(id, qty){
  const items = await (await fetch('data/items.json',{cache:'no-store'})).json();
  const it = items.find(x=>x.id===id);
  if(!it) { alert('Item missing'); return; }
  if(it.stock<=0){ alert('Out of stock'); return; }
  const cart = getCart();
  const existing = cart.find(x=>x.id===id);
  if(existing){
    if(existing.qty + qty > it.stock){ alert('Not enough stock'); return; }
    existing.qty += qty;
  } else {
    cart.push({id:id, qty: qty});
  }
  saveCart(cart);
  updateCartUI && updateCartUI();
}

function removeFromCart(id){
  let cart = getCart();
  cart = cart.filter(x=>x.id!==id);
  saveCart(cart);
  updateCartUI && updateCartUI();
}

async function renderCheckout(){
  const cart = getCart();
  const items = await (await fetch('data/items.json',{cache:'no-store'})).json();
  const area = document.getElementById('cart-area');
  if(!area) return;
  if(cart.length===0){ area.innerHTML='<p>Your cart is empty.</p>'; return; }
  let html = '<table><thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead><tbody>';
  let sum=0;
  cart.forEach(ci=>{
    const it = items.find(x=>x.id===ci.id) || {name:'(missing)',price:0,stock:0};
    const line = it.price * ci.qty;
    sum += line;
    html += `<tr><td>${it.name}</td><td>Ksh ${it.price}</td><td>${ci.qty}</td><td>Ksh ${line}</td><td><button onclick="removeFromCart(${ci.id})">Remove</button></td></tr>`;
  });
  html += `</tbody></table><div style="margin-top:12px"><strong>Total: Ksh ${sum}</strong></div>`;
  area.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', ()=>{
  // if checkout page is present
  if(document.getElementById('cart-area')) renderCheckout();
});