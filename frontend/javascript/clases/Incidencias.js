export class Incidencia {
  /**
   * Representa una incidencia en el sistema, basado en la tabla Incidencia.
   */
  constructor({
    id_incidencia = null,
    titulo = '',
    descripcion = '',
    fecha = null,       // Date o string ISO
    prioridad = '',     // Ej: 'Alta', 'Media', 'Baja'
    estado = '',        // Ej: 'Abierta', 'Cerrada', 'En progreso'
    id_usuario = null,  // Usuario que crea la incidencia
    id_recurso = null   // Recurso asociado
  } = {}) {
    this._id = id_incidencia;
    this._titulo = titulo;
    this._descripcion = descripcion;
    this._fecha = fecha;
    this._prioridad = prioridad;
    this._estado = estado;
    this._id_usuario = id_usuario;
    this._id_recurso = id_recurso;
  }

  // ======================
  // GETTERS Y SETTERS
  // ======================

  get id() { return this._id; }
  set id(valor) { this._id = valor; }

  get titulo() { return this._titulo; }
  set titulo(valor) { this._titulo = valor; }

  get descripcion() { return this._descripcion; }
  set descripcion(valor) { this._descripcion = valor; }

  get fecha() { return this._fecha; }
  set fecha(valor) { this._fecha = valor; }

  get prioridad() { return this._prioridad; }
  set prioridad(valor) { this._prioridad = valor; }

  get estado() { return this._estado; }
  set estado(valor) { this._estado = valor; }

  get id_usuario() { return this._id_usuario; }
  set id_usuario(valor) { this._id_usuario = valor; }

  get id_recurso() { return this._id_recurso; }
  set id_recurso(valor) { this._id_recurso = valor; }
}
