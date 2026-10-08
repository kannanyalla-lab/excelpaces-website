(function(){
const $=(s,c=document)=>c.querySelector(s),$$=(s,c=document)=>[...c.querySelectorAll(s)];
const hdr=$('#hdr');const onScroll=()=>hdr.classList.toggle('stuck',scrollY>8);onScroll();addEventListener('scroll',onScroll,{passive:true});
const burger=$('#burger'),nav=$('#nav');
burger&&burger.addEventListener('click',()=>{const o=nav.classList.toggle('open');burger.setAttribute('aria-expanded',o)});
// hero slider
const sl=$('#slider');
if(sl){const slides=$$('.slide',sl),dots=$$('.dots button',sl);let i=0,t;
 const go=n=>{i=(n+slides.length)%slides.length;slides.forEach((s,k)=>s.classList.toggle('on',k===i));dots.forEach((d,k)=>{d.classList.remove('on');if(k===i){void d.offsetWidth;d.classList.add('on')}})};
 const play=()=>{clearInterval(t);if(slides.length>1)t=setInterval(()=>go(i+1),6500)};
 dots.forEach((d,k)=>d.addEventListener('click',()=>{go(k);play()}));play();
 sl.addEventListener('mouseenter',()=>clearInterval(t));sl.addEventListener('mouseleave',play);}
// hero background photo follows the slide
const hb=$('#heroBg');
if(hb){const set=()=>{const a=$('.slide.on',sl||document);const img=a&&a.dataset.img;if(img){hb.style.backgroundImage="url('"+img+"')";hb.classList.add('on')}else hb.classList.remove('on')};set();
 if(sl){new MutationObserver(set).observe(sl,{subtree:true,attributes:true,attributeFilter:['class']})}}
// employment rows
const emp=$('#emp');
if(emp){const add=()=>{const r=document.createElement('div');r.className='emp-row';r.innerHTML='<input name="emp_hospital[]" placeholder="Hospital name"><input name="emp_title[]" placeholder="Job title"><input name="emp_years[]" placeholder="Years"><button type="button" aria-label="Remove row">−</button>';emp.insertBefore(r,$('.emp-add',emp))};
 emp.addEventListener('click',e=>{if(e.target.closest('.emp-add')){if($$('.emp-row',emp).length<10)add()}else if(e.target.closest('.emp-row button')){const rows=$$('.emp-row',emp);if(rows.length>1)e.target.closest('.emp-row').remove();else $$('input',rows[0]).forEach(i=>i.value='')}})}
// countdown
const c=$('.count');
if(c){const target=new Date(c.dataset.date).getTime();const u=k=>$('[data-u='+k+']',c);
 const tick=()=>{let d=Math.max(0,target-Date.now());const D=Math.floor(d/864e5);d%=864e5;const H=Math.floor(d/36e5);d%=36e5;const M=Math.floor(d/6e4);const S=Math.floor(d%6e4/1e3);
  [['d',D],['h',H],['m',M],['s',S]].forEach(([k,v])=>u(k).textContent=String(v).padStart(2,'0'))};tick();setInterval(tick,1000);}
// stations
const st=$('#stations');
if(st){const tabs=$$('.st-tabs button',st),ps=$$('.st-panel',st);
 const sel=n=>{tabs.forEach((b,k)=>{b.classList.toggle('on',k===n);b.setAttribute('aria-selected',k===n)});ps.forEach((p,k)=>p.classList.toggle('on',k===n))};
 tabs.forEach((b,k)=>{b.addEventListener('click',()=>sel(k));b.addEventListener('mouseenter',()=>{if(matchMedia('(hover:hover)').matches)sel(k)})});}
// testimonial carousel
const tc=$('#tcar');
if(tc){const step=()=>tc.firstElementChild.getBoundingClientRect().width+20;
 $('#tNext').addEventListener('click',()=>tc.scrollBy({left:step(),behavior:'smooth'}));$('#tPrev').addEventListener('click',()=>tc.scrollBy({left:-step(),behavior:'smooth'}));}
// reveal
const io='IntersectionObserver'in window?new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}}),{threshold:.12}):null;
$$('.reveal').forEach((el,k)=>{el.style.transitionDelay=(k%4)*70+'ms';io?io.observe(el):el.classList.add('in')});
// faculty modal
$$('.fac[data-bio]').forEach(f=>{const open=()=>{const m=document.createElement('div');m.className='modal';
  m.innerHTML='<div class="modal-c" role="dialog" aria-modal="true"><button class="modal-x" aria-label="Close">×</button><h3></h3><p class="role"></p><div class="prose"></div></div>';
  $('h3',m).textContent=$('h3',f).textContent;$('.role',m).textContent=$('.role',f).textContent;$('.prose',m).innerHTML=$('.bio',f).innerHTML;
  const close=()=>m.remove();m.addEventListener('click',e=>{if(e.target===m||e.target.className==='modal-x')close()});
  addEventListener('keydown',function k(e){if(e.key==='Escape'){close();removeEventListener('keydown',k)}});document.body.appendChild(m);$('.modal-x',m).focus()};
  f.addEventListener('click',open);f.addEventListener('keydown',e=>{if(e.key==='Enter')open()})});
// lightbox
$$('[data-lb]').forEach(a=>a.addEventListener('click',e=>{e.preventDefault();const l=document.createElement('div');l.className='lb';
 l.innerHTML='<div><img alt=""><p></p></div>';$('img',l).src=a.href;$('p',l).textContent=a.dataset.lb;l.addEventListener('click',()=>l.remove());
 addEventListener('keydown',function k(e){if(e.key==='Escape'){l.remove();removeEventListener('keydown',k)}});document.body.appendChild(l)}));
// hide sticky apply on apply page / near cta
if($('.formcard')){const s=$('.sticky-apply');s&&s.remove()}
})();
