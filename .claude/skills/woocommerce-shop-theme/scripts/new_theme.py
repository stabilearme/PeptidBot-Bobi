#!/usr/bin/env python3
"""Neues Child Theme aus dem Grundgerüst anlegen.

Beispiel:
  python3 new_theme.py --target ./theme/meinshop-pro --name "MeinShop Pro" \
      --slug meinshop-pro --prefix msp --url https://meinshop.de --author "MeinShop"

--prefix ersetzt das Kürzel des Grundgerüsts (alp_ / ALP_ / alp- / --alp-) überall
in PHP, CSS und JS. 2–5 Kleinbuchstaben, eindeutig (kein Plugin darf es nutzen).
"""
import argparse, os, re, shutil, sys

HERE = os.path.dirname(os.path.abspath(__file__))
SKELETON = os.path.join(HERE, '..', 'assets', 'theme-skeleton')

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--target', required=True)
    ap.add_argument('--name', required=True, help='Theme-Name, z. B. "MeinShop Pro"')
    ap.add_argument('--slug', required=True, help='Ordner/Text-Domain, z. B. meinshop-pro')
    ap.add_argument('--prefix', default='alp', help='Funktions-/CSS-Kürzel (Standard: alp)')
    ap.add_argument('--url', default='', help='Shop-Adresse')
    ap.add_argument('--author', default='')
    ap.add_argument('--parent', default='flatsome', help='Eltern-Theme (Template:)')
    a = ap.parse_args()

    if not re.fullmatch(r'[a-z]{2,5}', a.prefix):
        sys.exit('Prefix: 2–5 Kleinbuchstaben, z. B. msp')
    if not re.fullmatch(r'[a-z0-9-]+', a.slug):
        sys.exit('Slug: nur a-z, 0-9, Bindestrich')
    if os.path.exists(a.target):
        sys.exit(f'Ziel existiert schon: {a.target}')
    shutil.copytree(SKELETON, a.target)

    p_low, p_up = a.prefix, a.prefix.upper()
    for root, _, files in os.walk(a.target):
        for fn in files:
            if not fn.endswith(('.php', '.css', '.js', '.md')):
                continue
            path = os.path.join(root, fn)
            s = open(path, encoding='utf-8').read()
            o = s
            if p_low != 'alp':
                s = re.sub(r'\balp_', p_low + '_', s)
                s = re.sub(r'\bALP_', p_up + '_', s)
                s = re.sub(r'(?<![A-Za-z0-9])alp-', p_low + '-', s)   # CSS-Klassen, Handles, --alp-Variablen
                s = re.sub(r"'alp'", f"'{p_low}'", s)                 # Body-Klasse .alp
                s = re.sub(r'\.alp(?=[\s.,:{>\[)#+~]|$)', '.' + p_low, s)   # .alp / body.alp
                s = s.replace('_alp_', '_' + p_low + '_')                        # Meta-Schlüssel, Hook-Namen
                s = re.sub(r'\balp(?=3d)', p_low, s)                           # alp3d-Icons
                s = s.replace('AminoLabs Pro:', a.name + ':')
            s = s.replace('aminolabspro-pro', a.slug)
            if s != o:
                open(path, 'w', encoding='utf-8').write(s)

    css = os.path.join(a.target, 'style.css')
    s = open(css, encoding='utf-8').read()
    head = f"""/*
Theme Name:   {a.name}
Theme URI:    {a.url}
Description:  Child Theme für {a.url or a.name} auf Basis von {a.parent}. Inhalte in /inc/config.php und /data/.
Author:       {a.author or a.name}
Template:     {a.parent}
Version:      1.0.0
Requires at least: 6.4
Requires PHP: 8.0
Text Domain:  {a.slug}
License:      GPL-2.0-or-later
*/"""
    s = re.sub(r'/\*.*?\*/', head, s, count=1, flags=re.S)
    open(css, 'w', encoding='utf-8').write(s)
    print(f'Fertig: {a.target}\nNächster Schritt: inc/config.php (brand, Texte, Farben) und assets/css/tokens.css anpassen.')

if __name__ == '__main__':
    main()
