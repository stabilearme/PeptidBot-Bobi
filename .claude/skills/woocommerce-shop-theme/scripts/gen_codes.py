#!/usr/bin/env python3
"""Einmal-Gutscheincodes erzeugen: data/welcome-codes.php + CSV (z. B. für Brevo Pro-Tarif).
Aufruf: python3 gen_codes.py --prefix NEU15- --count 500 --theme ./theme/meinshop-pro --csv codes.csv"""
import argparse, secrets
A = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'   # ohne 0/O/1/I
ap = argparse.ArgumentParser(); ap.add_argument('--prefix', required=True); ap.add_argument('--count', type=int, default=500)
ap.add_argument('--theme', required=True); ap.add_argument('--csv', default='codes.csv'); a = ap.parse_args()
codes = set()
while len(codes) < a.count: codes.add(a.prefix + ''.join(secrets.choice(A) for _ in range(6)))
codes = sorted(codes)
with open(f'{a.theme}/data/welcome-codes.php', 'w') as f:
    f.write("<?php\ndefined( 'ABSPATH' ) || exit;\n\nreturn array(\n" + ''.join(f"\t'{c}',\n" for c in codes) + ");\n")
open(a.csv, 'w').write('\n'.join(codes) + '\n')
print(len(codes), 'Codes →', a.csv)
