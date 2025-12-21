import xml.etree.ElementTree as ET
from pathlib import Path

NAMESPACE = {"c": "http://www.uniovi.es"}
WIDTH, HEIGHT, MARGIN = 1000, 400, 50

def obtener_valor(elemento, ruta, tipo=float):
    nodo = elemento.find(ruta, NAMESPACE)
    return tipo(nodo.text.strip()) if nodo is not None and nodo.text else None

def extraer_puntos(xml_path):
    raiz = ET.parse(xml_path).getroot()
    tramos = raiz.findall(".//c:tramos/c:tramo", NAMESPACE)
    puntos = []
    distancia_acumulada = 0.0

    for tramo in tramos:
        distancia = obtener_valor(tramo, "c:distancia")
        altitud = obtener_valor(tramo, "c:altitud")
        if distancia is not None and altitud is not None:
            distancia_acumulada += distancia
            puntos.append((distancia_acumulada, altitud))
    return puntos

def escalar(puntos):
    max_d = max(d for d, _ in puntos)
    min_a = min(a for _, a in puntos)
    max_a = max(a for _, a in puntos)

    escala_x = (WIDTH - 2 * MARGIN) / max_d
    rango_altitud = max_a - min_a

    if rango_altitud == 0:
        # Todas las altitudes son iguales, dibujamos línea horizontal
        return [(MARGIN + d * escala_x, HEIGHT // 2) for d, _ in puntos]

    escala_y = (HEIGHT - 2 * MARGIN) / rango_altitud
    return [(MARGIN + d * escala_x, HEIGHT - MARGIN - (a - min_a) * escala_y) for d, a in puntos]

def generar_svg(puntos):
    escalados = escalar(puntos)

    # Añadir puntos para cerrar la polilínea al suelo
    x0, _ = escalados[0]
    xN, _ = escalados[-1]
    suelo_y = HEIGHT - MARGIN
    escalados_cerrados = escalados + [(xN, suelo_y), (x0, suelo_y), escalados[0]]

    polyline = " ".join(f"{x:.2f},{y:.2f}" for x, y in escalados_cerrados)

    etiquetas = [
        f'<text x="{x:.2f}" y="{HEIGHT - MARGIN + 15}" font-size="10" transform="rotate(90,{x:.2f},{HEIGHT - MARGIN + 15})">{int(d)}m</text>'
        f'<text x="{x:.2f}" y="{y - 5:.2f}" font-size="10">{int(a)}m</text>'
        for (d, a), (x, y) in zip(puntos, escalados)
    ]

    return f"""<?xml version="1.0" encoding="UTF-8"?>
<svg width="{WIDTH}" height="{HEIGHT}" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="white"/>
  <polygon points="{polyline}" fill="lightblue" stroke="red" stroke-width="2"/>
  {"".join(etiquetas)}
</svg>
"""


def main():
    xml_path = Path("circuitoEsquema.xml")
    svg_path = Path("altimetria.svg")
    puntos = extraer_puntos(xml_path)
    if not puntos:
        print("No se encontraron puntos válidos.")
        return
    svg = generar_svg(puntos)
    svg_path.write_text(svg, encoding="utf-8")
    print(f"[OK] SVG generado: {svg_path} con {len(puntos)} puntos")

if __name__ == "__main__":
    main()
