/*!
 *
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */ 
var SelectDependiente = function(config)
{
    if (this.instancias[config.id] instanceof SelectDependiente)
    {
        return this.instancias[config.id];
    }

    this.instancias[config.id] = this;

    defecto = 
    {
        id:          '',
        opciones:    {},
        dependiente: '',
        vacio:       false,
        ajax:        false,
        cache:       true,
        url:         '',
        params:      {},
        varref:      '_ref',
        varsoloref:  '_solo_ref'
    };

    for (var item in defecto)
    {
        this[item] = typeof config[item] === 'undefined' ? defecto[item] : config[item];
    }

    if (typeof this.dependiente === 'string' && this.dependiente.length > 0)
    {
        this.dependiente = new SelectDependiente({ id: this.dependiente });
    }

    this.grupo       = '';
    this.limpio      = false;
    this.fnsCambio   = [];
    this.select      = document.getElementById(this.id);
    
    this.iniciar();
    
    return this;
};

SelectDependiente.prototype.instancias = [];

SelectDependiente.prototype.iniciar = function()
{
    var self = this;

    for (var i in this.params)
    {
        if (this.params[i] === null)
        {
            this.params[i] = undefined;
        }
    }

    this.select.onchange = function() 
    {
        self.cambio(self.select.options[self.select.selectedIndex].value);
    }
    
    if (typeof this.dependiente === 'object') 
    {
        this.dependiente.agregarFnCambio(function(valor) 
        {
            self.mostrar(valor);
        });
    }

    var mostrarHtml = false;
    for (var i=0; i<this.select.options.length; i++)
    {
        var opt = this.select.options[i];
        if (typeof this.opciones['html'] === 'undefined')
        {
            this.opciones.html = {};
        }
        if (opt.value == '')
        {
            this.vacio = opt.text;
        }
        else
        {
            this.opciones['html'][opt.value] = opt.text;
        }
        mostrarHtml = true;
    }
    if (mostrarHtml === true)
    {
        this.mostrar('html');
    }
};

SelectDependiente.prototype.agregarFnCambio = function(fn)
{
    this.fnsCambio.push(fn);
};

SelectDependiente.prototype.cambio = function(valor)
{
    for (var fn in this.fnsCambio)
    {
        this.fnsCambio[fn](valor);
    }
};

SelectDependiente.prototype.agregarOpcion = function(valor, texto)
{
    var opcion = document.createElement('option');
    opcion.value = valor;
    opcion.text = texto;

    try 
    {
        this.select.add(opcion, null);
    } 
    catch(ex) 
    {
        this.select.add(opcion);
    }
    
    this.limpio = false;
};

SelectDependiente.prototype.buscarGrupoDesdeValor = function(valorBuscado)
{
    var grupoBuscado = undefined;

    if (typeof this.dependiente === 'object' && this.ajax === true)
    {
        this.params[this.varsoloref] = true;
        this.params[this.varref] = valorBuscado;
        
        jQuery.ajax(
        {
            url:      this.url,
            data:     this.params,
            type:     'POST',
            async:    false,
            dataType: 'json',
            context:  this,
            success:  function(data)
            {
                if (typeof data !== 'undefined')
                {
                    grupoBuscado = data;
                }
            }
        });         
    }
    else
    {
        for (var grupo in this.opciones) 
        {
            for (var valor in this.opciones[grupo]) 
            {
                if (valor == valorBuscado) 
                {
                    grupoBuscado = grupo;
                }
            }
        }
    }
    
    return grupoBuscado;
};

SelectDependiente.prototype.limpiar = function(forzar)
{
    var forzar = typeof forzar === 'undefined' ? false : forzar;
    var cantidad = this.select.options.length;
    
    for (var i=0; i<cantidad; ++i) 
    {
        this.select.remove(0);
    }
    
    if (this.vacio !== false) 
    {
        this.agregarOpcion('', this.vacio);
    }
    
    this.limpio = true;
    this.grupo = '';
    
    if (forzar === false) 
    {
        this.cambio();
    }
};

SelectDependiente.prototype.mostrar = function(grupo, forzar)
{
    this.select.disabled = true;

    var forzar = typeof forzar === 'undefined' ? false : forzar;

    if (this.limpio === false) 
    {
        this.limpiar(forzar);
    }
    
    if (typeof grupo !== 'undefined' && grupo.toString().length == 0)
    {
        grupo = undefined;
    }
    
    if (typeof grupo === 'undefined' && typeof this.dependiente === 'object') 
    {
        if (this.dependiente.select.selectedIndex >= 0)
        {
            var valor = this.dependiente.select.options[this.dependiente.select.selectedIndex].value;
            
            if (valor.toString().length > 0) 
            {
                grupo = valor;
            }
        }
    }

    if (typeof grupo !== 'undefined' || (this.ajax === true && typeof this.dependiente !== 'object')) 
    {
        this.grupo = grupo;
        
        if (this.ajax === true && typeof jQuery !== 'undefined')
        {
            if ( ! (this.cache === true && typeof this.opciones[this.grupo] !== 'undefined'))
            {   
                this.params[this.varsoloref] = false;
                this.params[this.varref] = grupo;
                
                jQuery.ajax(
                {
                    url:      this.url,
                    data:     this.params,
                    type:     'POST',
                    async:    false,
                    dataType: 'json',
                    context:  this,
                    success:  function(data)
                    {
                        this.opciones[this.grupo] = data;
                    }
                });
            }
        }
        
        for (var valor in this.opciones[this.grupo]) 
        {
            this.agregarOpcion(valor, this.opciones[this.grupo][valor]);
        }    
    }

    if (forzar === false && this.select.options.length > 0) 
    {
        this.select.selectedIndex = 0;
        this.cambio(this.select.options[this.select.selectedIndex].value);
    }
    
    this.select.disabled = false;
    
    return this;
};

SelectDependiente.prototype.seleccionar = function(valor, forzar)
{
    var forzar = typeof forzar === 'undefined' ? false : forzar;    
    
    for (i=0; i<this.select.options.length; i++)
    {
        if (this.select.options[i].value == valor) 
        {
            this.select.selectedIndex = i;
            
            if (forzar === false) 
            {
                this.cambio(valor);
            }
            
            break;
        }
    }
    
    if (typeof this.dependiente === 'object')
    {
        this.dependiente.mostrar(this.dependiente.buscarGrupoDesdeValor(this.grupo), true);
        this.dependiente.seleccionar(this.grupo, true);
    }
};
