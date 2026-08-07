// load-items.js - loads data/items.json and renders products into #products
async function fetchItems(){
  const res = await fetch('data/items.json', {cache:'no-store'});
  const items = await res.json();
  return items;
}

function stockBadge(stock){
  if(stock<=0) return {text: 'Out of Stock', cls:'red'};
  if(stock<=5) return {text: 'Few Left', cls:'yellow'};
  return {text: 'In Stock', cls:'green'};
}

async function renderProducts(containerId='products'){
  const productsEl = document.getElementById(containerId);
  if(!productsEl) return;
  const items = await fetchItems();
  productsEl.innerHTML='';
  items.forEach(it=>{
    const badge = stockBadge(it.stock);
    const div = document.createElement('div');
    div.className='product';
    div.innerHTML = `
      <img src="${it.image || 'images/placeholder.png'}" alt="${it.name}">
      <div class="product-info">
        <h3>${it.name}</h3>
        <div>Price: Ksh ${it.price}</div>
      </div>
      <div class="product-actions">
        <div class="badge ${badge.cls}">${badge.text}</div><br><br>
        <button class="btn btn-primary add-to-cart" data-id="${it.id}">Add to Cart</button>
      </div>
    `;
    productsEl.appendChild(div);
  });
  // attach add-to-cart handlers (after rendering)
  document.querySelectorAll('.add-to-cart').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
      const id = Number(e.currentTarget.dataset.id);
      const items = await fetchItems();
      const it = items.find(x=>x.id===id);
      if(!it) return alert('Item not found');
      if(it.stock<=0) return alert('Item is out of stock');
      addToCart(id,1);
      alert('Added to cart');
    });
  });
}

document.addEventListener('DOMContentLoaded', ()=>{
  renderProducts();
});