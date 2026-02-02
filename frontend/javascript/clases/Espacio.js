export class Espacio {
  /**
   * Representa un espacio del sistema, basado en la tabla Recurso y Espacio.
   */
  constructor({
    id_espacio = null,       // id de Espacio (clave primaria)
    descripcion = '',        // descripción del recurso
    tipo = 'Espacio',        // tipo de recurso
    activo = true,           // si el recurso está activo
    especial = false,        // si es un recurso especial
    numero_planta = null,    // planta donde se encuentra
    id_edificio = null,      // edificio donde se encuentra
    nombre = '',             // nombre del espacio
    capacidad = 0,           // capacidad del espacio
    caracteristicas = []     // array de objetos o strings
  } = {}) {
    this._id_espacio = id_espacio;
    this._descripcion = descripcion;
    this._tipo = tipo;
    this._activo = Boolean(activo);
    this._especial = Boolean(especial);
    this._numero_planta = numero_planta;
    this._id_edificio = id_edificio;
    this._nombre = nombre;
    this._capacidad = capacidad;
    this._caracteristicas = caracteristicas; // array de strings o ids de caracteristicas
  }

  // ======================
  // GETTERS Y SETTERS
  // ======================

  get id_espacio() { return this._id_espacio; }
  set id_espacio(valor) { this._id_espacio = valor; }

  get descripcion() { return this._descripcion; }
  set descripcion(valor) { this._descripcion = valor; }

  get tipo() { return this._tipo; }
  set tipo(valor) { this._tipo = valor; }

  get activo() { return this._activo; }
  set activo(valor) { this._activo = Boolean(valor); }

  get especial() { return this._especial; }
  set especial(valor) { this._especial = Boolean(valor); }

  get numero_planta() { return this._numero_planta; }
  set numero_planta(valor) { this._numero_planta = valor; }

  get id_edificio() { return this._id_edificio; }
  set id_edificio(valor) { this._id_edificio = valor; }

  get nombre() { return this._nombre; }
  set nombre(valor) { this._nombre = valor; }

  get capacidad() { return this._capacidad; }
  set capacidad(valor) { this._capacidad = valor; }

  get caracteristicas() { return this._caracteristicas; }
  set caracteristicas(valor) { this._caracteristicas = valor; }
}
