(function(){
const $$=(s,c=document)=>[...c.querySelectorAll(s)];
// rich text editor
$$('.rte').forEach(box=>{
  const ta=$$('textarea',box)[0];
  const tb=document.createElement('div');tb.className='rte-tb';
  const ed=document.createElement('div');ed.className='rte-ed';ed.contentEditable='true';ed.innerHTML=ta.value;
  const src=document.createElement('textarea');src.className='rte-src';src.hidden=true;
  const cmd=(c,v)=>{ed.focus();document.execCommand(c,false,v||null)};
  [['B','bold','Bold'],['I','italic','Italic'],['H2','h2','Heading'],['H3','h3','Sub-heading'],['• List','ul','Bullet list'],['1. List','ol','Numbered list'],['“ ”','quote','Quote'],['🔗','link','Link'],['✕','clear','Clear formatting']].forEach(([t,c,title])=>{
    const b=document.createElement('button');b.type='button';b.textContent=t;b.title=title;
    b.addEventListener('mousedown',e=>e.preventDefault());
    b.addEventListener('click',()=>{
      if(c==='bold')cmd('bold');else if(c==='italic')cmd('italic');
      else if(c==='h2'||c==='h3')cmd('formatBlock',c);else if(c==='ul')cmd('insertUnorderedList');else if(c==='ol')cmd('insertOrderedList');
      else if(c==='quote')cmd('formatBlock','blockquote');
      else if(c==='link'){const u=prompt('Link address (https://…)');if(u&&/^(https?:|mailto:|tel:|\/)/i.test(u))cmd('createLink',u)}
      else if(c==='clear'){cmd('removeFormat');cmd('formatBlock','p')}
    });tb.appendChild(b)});
  const tg=document.createElement('button');tg.type='button';tg.textContent='</> HTML';tg.addEventListener('click',()=>{
    if(src.hidden){src.value=ed.innerHTML;src.hidden=false;ed.hidden=true}else{ed.innerHTML=src.value;src.hidden=true;ed.hidden=false}});tb.appendChild(tg);
  box.append(tb,ed,src);
  box.closest('form').addEventListener('submit',()=>{ta.value=src.hidden?ed.innerHTML:src.value});
});
// colour hex label
$$('input[type=color]').forEach(i=>i.addEventListener('input',()=>{const c=i.parentNode.querySelector('code');if(c)c.textContent=i.value}));
})();
