"""Beispiel: Mail-Bilder (JPG) für Wochenangebote bauen – Produktfotos als „Vial-Reihe“ auf dunkler Bühne.
Voraussetzungen: pip install pillow fonttools brotli; Schriften aus assets/fonts als TTF (fontTools) ins Arbeitsverzeichnis;
src/<SKU>.webp = Produktfotos (1024px), ../mail_weeks.json = Liste [{key,title,percent,items:[{sku,price?}]}], /tmp/p.json = Store-API-Produkte.
Ausgabe: out/deal-<key>.jpg → in <theme>/assets/mail/ kopieren und in den Mails per URL einbinden."""
import json,re
from PIL import Image,ImageDraw,ImageFilter,ImageFont
W,H=1120,760
MINT=(110,218,180); INK=(26,31,46)
def font(n,size,wght=None):
    f=ImageFont.truetype(n+'.ttf',size)
    if wght:
        try: f.set_variation_by_axes([wght] if 'fraunces' not in n else [wght] if False else f.get_variation_axes() and [ax['default'] if ax['name']!=b'Weight' and ax.get('name')!='Weight' else wght for ax in f.get_variation_axes()])
        except Exception: pass
    return f
def fvar(n,size,wght):
    f=ImageFont.truetype(n+'.ttf',size)
    try:
        axes=f.get_variation_axes(); vals=[]
        for ax in axes:
            nm=ax.get('name'); nm=nm.decode() if isinstance(nm,bytes) else nm
            vals.append(wght if 'eight' in str(nm) else ax['default'])
        f.set_variation_by_axes(vals)
    except Exception as e: pass
    return f
def bg():
    im=Image.new('RGB',(W,H),(7,15,22))
    g=Image.new('L',(W,H),0); d=ImageDraw.Draw(g)
    for r in range(0,900,6):
        d.ellipse((W/2-r*1.3,H*1.05-r,W/2+r*1.3,H*1.05+r),fill=max(0,255-int(r*0.33)))
    g=g.filter(ImageFilter.GaussianBlur(40))
    col=Image.new('RGB',(W,H),(18,58,64))
    im=Image.composite(col,im,g.point(lambda v:int(v*0.8)))
    glow=Image.new('L',(W,H),0); ImageDraw.Draw(glow).ellipse((W*0.18,H*0.80,W*0.82,H*0.98),fill=150)
    glow=glow.filter(ImageFilter.GaussianBlur(45))
    im=Image.composite(Image.new('RGB',(W,H),MINT),im,glow.point(lambda v:int(v*0.45)))
    return im
def card(sku,h):
    src=Image.open('src/'+sku+'.webp').convert('RGB'); s=src.size[0]
    cw=int(s*0.5); x0=(s-cw)//2
    crop=src.crop((x0,int(s*0.06),x0+cw,int(s*0.06)+int(cw*5/3) if int(s*0.06)+int(cw*5/3)<=s else s))
    w=int(h*crop.size[0]/crop.size[1]); crop=crop.resize((w,h),Image.LANCZOS)
    m=Image.new('L',(w,h),0); ImageDraw.Draw(m).rounded_rectangle((0,0,w-1,h-1),radius=int(h*0.05),fill=255)
    return crop,m
def paste_card(im,sku,cx,bottom,h):
    c,m=card(sku,h); w=c.size[0]; x=int(cx-w/2); y=int(bottom-h)
    sh=Image.new('L',(W,H),0); ImageDraw.Draw(sh).rounded_rectangle((x+6,y+22,x+w+6,y+h+22),radius=30,fill=170)
    sh=sh.filter(ImageFilter.GaussianBlur(26)); im.paste(Image.new('RGB',(W,H),(0,0,0)),(0,0),sh)
    im.paste(c,(x,y),m)
    ImageDraw.Draw(im).rounded_rectangle((x,y,x+w-1,y+h-1),radius=int(h*0.05),outline=(255,255,255,20),width=1)
def pill(d,xy,text,f,fill,outline,color,pad=(18,10),anchor='lt'):
    b=d.textbbox((0,0),text,font=f); tw,th=b[2]-b[0],b[3]-b[1]
    x,y=xy
    if anchor=='mt': x=x-(tw+2*pad[0])/2
    d.rounded_rectangle((x,y,x+tw+2*pad[0],y+th+2*pad[1]+6),radius=(th+2*pad[1])//2+3,fill=fill,outline=outline,width=2)
    d.text((x+pad[0],y+pad[1]-b[1]+3),text,font=f,fill=color)
    return tw+2*pad[0]
def short(n): return re.sub(r'\s*\d+([.,]\d+)?\s*(mg|ml|mcg)\b.*$','',re.sub(r'\s*\([^)]*\)','',n),flags=re.I).strip()
ps={p['sku']:p for p in json.load(open('/tmp/p.json'))}
weeks=json.load(open('../mail_weeks.json'))
mono=fvar('jetbrains-mono-latin-700-normal',24,700); body=fvar('manrope-latin-wght-normal',20,700); disp=fvar('fraunces-latin-wght-normal',50,500); dispS=fvar('fraunces-latin-wght-normal',40,500)
out=[]
for w in weeks:
    im=bg(); d=ImageDraw.Draw(im)
    skus=[i['sku'] for i in w['items']]; n=len(skus); bottom=H-150
    if n==1: paste_card(im,skus[0],W/2,bottom,560)
    elif n==2:
        paste_card(im,skus[0],W/2-120,bottom-10,500); paste_card(im,skus[1],W/2+120,bottom,540)
    else:
        paste_card(im,skus[0],W/2-215,bottom-8,480); paste_card(im,skus[2],W/2+215,bottom-8,480); paste_card(im,skus[1],W/2,bottom,560)
    d=ImageDraw.Draw(im)
    if n>1: pill(d,(36,34),f'{n}er-Stack',mono,(11,26,38),MINT,MINT)
    else: pill(d,(36,34),'Wochenangebot',mono,(11,26,38),MINT,MINT)
    # badge
    cx,cy,r=W-120,120,86
    d.ellipse((cx-r,cy-r,cx+r,cy+r),fill=MINT)
    if n>1:
        t1='AKTION'; t2=f"−{w['percent']} %"; f2=disp
    else:
        price=w['items'][0]['price']; t1='NUR'; t2=f"{price:.2f}".replace('.',',')+' €'; f2=dispS
    fb=fvar('manrope-latin-wght-normal',17,800)
    d.text((cx,cy-30),t1,font=fb,fill=INK,anchor='mm'); d.text((cx,cy+14),t2,font=f2,fill=INK,anchor='mm')
    # names
    names=[short(ps[s]['name']) for s in skus]
    fn=fvar('jetbrains-mono-latin-700-normal',22,700)
    widths=[d.textbbox((0,0),t,font=fn)[2]+36+12 for t in names]; x=(W-sum(widths)+12)/2
    for t,wd in zip(names,widths):
        pill(d,(x,H-92),t,fn,(34,48,58),(80,98,110),(255,255,255)); x+=wd
    fname=f"deal-{w['key']}.jpg"; im.save('out/'+fname,quality=86,optimize=True,progressive=True); out.append(fname)
print(out)
