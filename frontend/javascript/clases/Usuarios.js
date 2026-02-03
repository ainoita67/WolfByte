export class UsuarioSistema {
  constructor({
    id_usuario = null,
    nombre = '',
    correo = '',
    contrasena = '',
    token = null,
    expira_token = null,
    usuario_activo = true,
    rol = { id_rol: null, nombre_rol: '', descripcion_rol: '' }
  } = {}) {
    this._id_usuario = id_usuario;
    this._nombre = nombre;
    this._correo = correo;
    this._contrasena = contrasena;
    this._token = token;
    this._expira_token = expira_token;
    this._usuario_activo = Boolean(usuario_activo);
    this._rol = {
      id_rol: rol.id_rol || null,
      nombre_rol: rol.nombre_rol || '',
      descripcion_rol: rol.descripcion_rol || ''
    };
  }

  // ======================
  // GETTERS Y SETTERS
  // ======================

  get id_usuario() {
    return this._id_usuario;
  }

  set id_usuario(valor) {
    this._id_usuario = valor;
  }

  get nombre() {
    return this._nombre;
  }

  set nombre(valor) {
    this._nombre = valor;
  }

  get correo() {
    return this._correo;
  }

  set correo(valor) {
    this._correo = valor;
  }

  get contrasena() {
    return this._contrasena;
  }

  set contrasena(valor) {
    this._contrasena = valor;
  }

  get token() {
    return this._token;
  }

  set token(valor) {
    this._token = valor;
  }

  get expira_token() {
    return this._expira_token;
  }

  set expira_token(valor) {
    this._expira_token = valor;
  }

  get usuario_activo() {
    return this._usuario_activo;
  }

  set usuario_activo(valor) {
    this._usuario_activo = Boolean(valor);
  }

  get rol() {
    return this._rol;
  }

  set rol(valor) {
    this._rol = {
      id_rol: valor.id_rol || null,
      nombre_rol: valor.nombre_rol || '',
      descripcion_rol: valor.descripcion_rol || ''
    };
  }
}
