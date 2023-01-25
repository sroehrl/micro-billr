import { Manager } from "https://cdn.socket.io/4.4.1/socket.io.esm.min.js";
// import onChange from "/js/onChange.js"
export const lenkradSocketManager = new Manager("ws://localhost:3000",{
    autoConnect: false
});

let storeIt = false;

export const SyncEntity = async (path) => {
    const j = await fetch(path)
    const dataObj = await j.json()
    const main = lenkradSocketManager.socket(dataObj.lrs.namespace, {auth:{token:dataObj.lrs.token}})
    delete dataObj.lrs;

    let updater;

    dataObj.onChange = cb => updater = cb;
    main.connect()

    let info = {changed:{}}

    const internalData = watcher(dataObj, (property, value, previousValue, applyData) => {
        info.changed = {
            property,
            value,
            from: previousValue
        }
        if(previousValue !== value && storeIt){
            fetch(path, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dataObj)
            })
        }

    })
    main.on('update', (data) => {
        Object.keys(internalData).forEach(key => {
            if(data[key] !== internalData[key] && key !== 'onChange'){
                internalData[key] = data[key]
            }
        })
        updater(info);
    })
    return internalData;


}

export const BindHTML = (syncedObject) => {
    return {
        outputMatch(matchObject){
            Object.keys(matchObject).forEach(key => {
                let ele = document.querySelector(key);
                ele.value = syncedObject[matchObject[key].property];
                // I do it
                ele.addEventListener(matchObject[key].event || 'blur', ev => {
                    storeIt = true;
                    syncedObject[matchObject[key].property] = ev.target.value;
                })
                // you do it
                syncedObject.onChange(info => {
                    console.log(info)
                    storeIt = false;
                    if(info.changed.property === matchObject[key].property){
                        ele.value = syncedObject[matchObject[key].property];
                    }

                })
            })
        }
    }

}

const isPrimitive = value => value === null || (typeof value !== 'object' && typeof value !== 'function');
const concatPath = (path, property) => {
    if (property && property.toString) {
        if (path)path += '.';
        path += property.toString();
    }
    return path;
};

const proxyTarget = Symbol('ProxyTarget');

const watcher = (object, onChange) => {
    let inApply = false;
    let changed = false;
    const propCache = new WeakMap();
    const pathCache = new WeakMap();
    const handleChange = (path, property, previous, value) => {
        if (!inApply) onChange.call(proxy, concatPath(path, property), value, previous);
        else if (!changed)  changed = true;
    };
    const getOwnPropertyDescriptor = (target, property) => {
        let props = propCache.get(target);
        if (props) return props;
        props = new Map();
        propCache.set(target, props);
        let prop = props.get(property);
        if (!prop) {
            prop = Reflect.getOwnPropertyDescriptor(target, property);
            props.set(property, prop);
        }
        return prop;
    };
    const invalidateCachedDescriptor = (target, property) => {
        const props = propCache.get(target);
        if (props) props.delete(property);
    };
    const handler = {
        get(target, property, receiver) {
            if (property === '___target___' ) return target;
            if (property === proxyTarget ) return target;
            const value = Reflect.get(target, property, receiver);
            if (isPrimitive(value) || property === 'constructor') return value;
            // Preserve invariants
            const descriptor = getOwnPropertyDescriptor(target, property);
            if (descriptor && !descriptor.configurable) {
                if (descriptor.set && !descriptor.get) return undefined;
                if (descriptor.writable === false) return value;
            }
            pathCache.set(value, concatPath(pathCache.get(target), property));
            return new Proxy(value, handler);
        },
        set(target, property, value, receiver) {
            if (value && value[proxyTarget] !== undefined) value = value[proxyTarget];
            const previous = Reflect.get(target, property, receiver);
            const result = Reflect.set(target, property, value);
            if (previous !== value) handleChange(pathCache.get(target), property, previous, value);
            return result;
        },
        defineProperty(target, property, descriptor) {
            const result = Reflect.defineProperty(target, property, descriptor);
            invalidateCachedDescriptor(target, property);
            handleChange(pathCache.get(target), property, undefined, descriptor.value);
            return result;
        },
        deleteProperty(target, property) {
            const previous = Reflect.get(target, property);
            const result = Reflect.deleteProperty(target, property);
            invalidateCachedDescriptor(target, property);
            handleChange(pathCache.get(target), property, previous);
            return result;
        },
        apply(target, thisArg, argumentsList) {
            if (!inApply) {
                inApply = true;
                const result = Reflect.apply(target, thisArg, argumentsList);
                if (changed) onChange();
                inApply = false;
                changed = false;
                return result;
            }
            return Reflect.apply(target, thisArg, argumentsList);
        },
    };
    pathCache.set(object, '');
    const proxy = new Proxy(object, handler);
    return proxy;
};
