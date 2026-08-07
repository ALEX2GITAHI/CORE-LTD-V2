// admin.js - simple client-side admin to edit items in memory and download JSON
async function fetchItems(){
  const res = await fetch('data/items.json', {cache:'no-store'});
  return await res.json();
}

function badgeForStock(stock){
  if(stock<=0) return '<span class="badge red">Out of Stock</span>';
  if(stock<=5) return '<span class="badge yellow">Few Left</span>';
  return '<span class="badge green">In Stock</span>';
}

async function renderAdmin(){
  const area = document.getElementById('adminArea');
  const items = await fetchItems();
  let html = `<table><thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead><tbody>`;
  items.forEach(it=>{
    html += `<tr>
      <td>${it.id}</td>
      <td style="width:80px"><img src="${it.image||'images/placeholder.png'}" style="width:60px;height:40px;object-fit:cover"></td>
      <td>${it.name}</td>
      <td>Ksh ${it.price}</td>
      <td>${it.stock}</td>
      <td>${badgeForStock(it.stock)}</td>
      <td><button class="btn" onclick="editItem(${it.id})">Edit</button></td>
    </tr>`;
  });
  html += '</tbody></table>';
  area.innerHTML = html;
}

function openModal(){ document.getElementById('modal').style.display='flex'; }
function closeModal(){ document.getElementById('modal').style.display='none'; }

document.addEventListener('DOMContentLoaded', async ()=>{
  await renderAdmin();
  document.getElementById('btnAdd').addEventListener('click', ()=>{
    document.getElementById('modalTitle').innerText='Add Item';
    document.getElementById('itemId').value='';
    document.getElementById('name').value='';
    document.getElementById('price').value='0';
    document.getElementById('stock').value='0';
    document.getElementById('image').value='images/placeholder.png';
    openModal();
  });
  document.getElementById('btnCancel').addEventListener('click', closeModal);
  document.getElementById('btnDownload').addEventListener('click', async ()=>{
    const items = await fetchItems();
    const blob = new Blob([JSON.stringify(items, null, 2)],{type:'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = 'items.json'; a.click(); URL.revokeObjectURL(url);
  });
  document.getElementById('btnPrint').addEventListener('click', ()=>{ window.print(); });
  document.getElementById('itemForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const id = document.getElementById('itemId').value;
    const name = document.getElementById('name').value;
    const price = Number(document.getElementById('price').value);
    const stock = Number(document.getElementById('stock').value);
    const image = document.getElementById('image').value;
    const items = await fetchItems();
    if(id){
      // edit existing
      const idx = items.findIndex(x=>x.id==Number(id));
      if(idx>=0){ items[idx].name = name; items[idx].price = price; items[idx].stock = stock; items[idx].image = image; }
    } else {
      // add new
      const newId = items.length? Math.max(...items.map(x=>x.id))+1 : 1;
      items.push({id:newId, name, price, stock, image});
    }
    // update in-memory by creating a blob and reloading the admin view using a data URL trick
    const blob = new Blob([JSON.stringify(items, null, 2)],{type:'application/json'});
    const url = URL.createObjectURL(blob);
    // replace fetch mock by overriding window.__items_blob_url for this session
    window.__items_override_url = url;
    closeModal();
    await renderAdmin();
    alert('Updated in browser. Click "Download JSON" to save the file and upload to the server to persist.');
  });
});

// override fetch for data/items.json if override exists
(function(){
  const orig = window.fetch;
  window.fetch = function(resource, opts){
    if(resource==='data/items.json' && window.__items_override_url){
      return orig(window.__items_override_url, opts);
    }
    return orig(resource, opts);
  }
})();