#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import xml.etree.ElementTree as ET
from pathlib import Path
import re

NS = {"c": "http://www.uniovi.es"}

def parse_tiempo_iso8601(duracion: str) -> str:
    """
    Convierte una duración ISO 8601 tipo PT41M52.321S a formato MM:SS.
    """
    m = re.match(r"PT(\d+)M(\d+)", duracion)
    if m:
        minutos = int(m.group(1))
        segundos = int(m.group(2))
        return f"{minutos:02d}:{segundos:02d}"
    return duracion

def main():
    xml_path = Path("circuitoEsquema.xml")
    out_html = Path("InfoCircuito.html")

    raiz = ET.parse(xml_path).getroot()

    nombre = raiz.findtext(".//c:nombre", namespaces=NS)

    # Datos generales
    longitud = raiz.find(".//c:longitudCircuito", NS).text + " metros"
    anchura = raiz.find(".//c:anchura", NS).text + " metros"
    fecha = raiz.find(".//c:fecha", NS).text
    hora = raiz.find(".//c:horaInicio", NS).text
    vueltas = raiz.find(".//c:vueltas", NS).text
    localidad = raiz.find(".//c:localidad", NS).text
    pais = raiz.find(".//c:pais", NS).text
    patrocinador = raiz.find(".//c:patrocinador", NS).text

    # Referencias
    refs = raiz.findall(".//c:referencias/c:referencia", NS)

    # Galería fotos
    fotos = raiz.findall(".//c:galeriaFotos/c:foto", NS)

    # Galería videos
    videos = raiz.findall(".//c:galeriaVideos/c:video", NS)

    # Vencedor
    vencedor = raiz.find(".//c:vencedor", NS)
    nombre_v = vencedor.findtext("c:nombre", namespaces=NS)
    tiempo_v = parse_tiempo_iso8601(vencedor.findtext("c:tiempo", namespaces=NS))

    # Clasificación mundial
    pilotos = raiz.findall(".//c:clasificacionMundial/c:piloto", NS)

    # Construcción HTML
    html = f"""<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Ángela Nistal Guerrero"/>
    <meta name="description" content="Información del circuito {nombre}"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>MotoGP-Circuito</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="estilo/layout.css"/>
</head>
<body>
<header>
    <h1><a href="index.html">MotoGP Desktop</a></h1>
    <nav>
        <a href="index.html">Inicio</a>
        <a href="piloto.html">Piloto</a>
        <a href="circuito.html">Circuito</a>
        <a href="meteorologia.html">Meteorologia</a>
        <a href="clasificaciones.html">Clasificaciones</a>
        <a href="juegos.html">Juegos</a>
        <a href="ayuda.html">Ayuda</a>
    </nav>
</header>
<main>
    <h2>{nombre}</h2>
    <section>
        <h3>Datos generales</h3>
        <ul>
            <li>Longitud: {longitud}</li>
            <li>Anchura: {anchura}</li>
            <li>Fecha: {fecha}</li>
            <li>Hora de inicio: {hora}</li>
            <li>Vueltas: {vueltas}</li>
            <li>Localidad: {localidad}</li>
            <li>País: {pais}</li>
            <li>Patrocinador: {patrocinador}</li>
        </ul>
    </section>
    <section>
        <h3>Referencias</h3>
        <ul>
"""
    for r in refs:
        html += f'            <li><a href="{r.get("src")}">{r.text}</a></li>\n'
    html += """        </ul>
    </section>
    <section>
        <h3>Galería de fotos</h3>
"""
    for f in fotos:
        src = "../" + f.get("src")
        alt = f.get("alt")
        html += f"""        <figure>
            <img src="{src}" alt="{alt}"/>
            <figcaption>{alt}</figcaption>
        </figure>
"""
    html += """    </section>
    <section>
        <h3>Galería de videos</h3>
"""
    for v in videos:
        src = "../" + v.get("src")
        html += f"""        <video controls>
            <source src="{src}" type="video/mp4"/>
            Tu navegador no soporta video HTML5.
        </video>
"""
    html += f"""    </section>
    <section>
        <h3>Vencedor</h3>
        <p>{nombre_v} — Tiempo: {tiempo_v}</p>
    </section>
    <section>
        <h3>Clasificación mundial</h3>
        <ol>
"""
    for p in pilotos:
        html += f'            <li>{p.text}</li>\n'
    html += """        </ol>
    </section>
</main>
</body>
</html>
"""

    out_html.write_text(html, encoding="utf-8")
    print(f"[OK] HTML generado: {out_html}")

if __name__ == "__main__":
    main()
