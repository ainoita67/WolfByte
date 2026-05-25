// ======================
// CLASE BASE RECURSO
// ======================
export class Recurso {
  /**
   * Representa un recurso genérico (tabla Recurso)
   */
  constructor({
    id_recurso = null,
    tipo = 'Espacio',        // 'Espacio' o 'Material'
    descripcion = '',        // descripción del recurso
    activo = true,
    especial = false,
    numero_planta = null,
    id_edificio = null
  } = {}) {
    this._id = id_recurso;
    this._tipo = tipo;
    this._descripcion = descripcion;
    this._activo = Boolean(activo);
    this._especial = Boolean(especial);
    this._numero_planta = numero_planta;
    this._id_edificio = id_edificio;
  }

  // ======================
  // GETTERS Y SETTERS
  // ======================
  get id() { return this._id; }
  set id(valor) { this._id = valor; }

  get tipo() { return this._tipo; }
  set tipo(valor) { this._tipo = valor; }

  get descripcion() { return this._descripcion; }
  set descripcion(valor) { this._descripcion = valor; }

  get activo() { return this._activo; }
  set activo(valor) { this._activo = Boolean(valor); }

  get especial() { return this._especial; }
  set especial(valor) { this._especial = Boolean(valor); }

  get numero_planta() { return this._numero_planta; }
  set numero_planta(valor) { this._numero_planta = valor; }

  get id_edificio() { return this._id_edificio; }
  set id_edificio(valor) { this._id_edificio = valor; }
}

// ======================
// CLASE MATERIAL
// ======================
export class Material extends Recurso {
  /**
   * Representa un material (tabla Material) que es un tipo de recurso
   */
  constructor({
    id_recurso = null,
    descripcion = '',
    unidades = 0,
    activo = true,
    especial = false,
    numero_planta = null,
    id_edificio = null
  } = {}) {
    super({ id_recurso, tipo: 'Material', descripcion, activo, especial, numero_planta, id_edificio });
    this._unidades = unidades;
  }

  get unidades() { return this._unidades; }
  set unidades(valor) { this._unidades = valor; }
}

// ======================
// CLASE ESPACIO RECURSO
// ======================
export class EspacioRecurso extends Recurso {
  /**
   * Representa un recurso que es un espacio
   * @param {Object} espacio - objeto de tipo Espacio
   */
  constructor({
    id_recurso = null,
    descripcion = '',
    espacio = null, // objeto Espacio
    activo = true,
    especial = false,
    numero_planta = null,
    id_edificio = null
  } = {}) {
    super({ id_recurso, tipo: 'Espacio', descripcion, activo, especial, numero_planta, id_edificio });
    this._espacio = espacio;
  }

  get espacio() { return this._espacio; }
  set espacio(valor) { this._espacio = valor; }
}
