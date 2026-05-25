export class Reserva {
  // ======================
  // PERMISOS POR ROL
  // ======================
  static PERMISOS_POR_ROL = {
    admin: ['CREAR_RESERVA', 'AUTORIZAR_RESERVA'],
    profesor: ['CREAR_RESERVA'],
    alumno: []
  };

  // ======================
  // CONSTRUCTOR COMPLETO
  // ======================
  constructor({
    id_reserva = null,
    asignatura = '',
    grupo = '',
    profesor = '',
    inicio = null,
    fin = null,
    observaciones = '',
    autorizada = false,
    id_usuario = null,
    id_usuario_autoriza = null,
    tipo = 'Reserva_espacio', // o 'Reserva_material'
    espacios = [],
    materiales = [],
    necesidades = [],
    liberaciones = [],
    permanente = null
  } = {}) {
    this._id = id_reserva;
    this._asignatura = asignatura;
    this._grupo = grupo;
    this._profesor = profesor;
    this._inicio = inicio;
    this._fin = fin;
    this._observaciones = observaciones;
    this._autorizada = Boolean(autorizada);
    this._id_usuario = id_usuario;
    this._id_usuario_autoriza = id_usuario_autoriza;
    this._tipo = tipo;

    this._espacios = espacios;
    this._materiales = materiales;
    this._necesidades = necesidades;
    this._liberaciones = liberaciones;
    this._permanente = permanente;
  }

  // ======================
  // GETTERS Y SETTERS
  // ======================

  get id() { return this._id; }
  set id(valor) { this._id = valor; }

  get asignatura() { return this._asignatura; }
  set asignatura(valor) { this._asignatura = valor; }

  get grupo() { return this._grupo; }
  set grupo(valor) { this._grupo = valor; }

  get profesor() { return this._profesor; }
  set profesor(valor) { this._profesor = valor; }

  get inicio() { return this._inicio; }
  set inicio(valor) { this._inicio = valor; }

  get fin() { return this._fin; }
  set fin(valor) { this._fin = valor; }

  get observaciones() { return this._observaciones; }
  set observaciones(valor) { this._observaciones = valor; }

  get autorizada() { return this._autorizada; }
  set autorizada(valor) { this._autorizada = Boolean(valor); }

  get id_usuario() { return this._id_usuario; }
  set id_usuario(valor) { this._id_usuario = valor; }

  get id_usuario_autoriza() { return this._id_usuario_autoriza; }
  set id_usuario_autoriza(valor) { this._id_usuario_autoriza = valor; }

  get tipo() { return this._tipo; }
  set tipo(valor) { this._tipo = valor; }

  get espacios() { return this._espacios; }
  set espacios(valor) { this._espacios = valor; }

  get materiales() { return this._materiales; }
  set materiales(valor) { this._materiales = valor; }

  get necesidades() { return this._necesidades; }
  set necesidades(valor) { this._necesidades = valor; }

  get liberaciones() { return this._liberaciones; }
  set liberaciones(valor) { this._liberaciones = valor; }

  get permanente() { return this._permanente; }
  set permanente(valor) { this._permanente = valor; }
}
