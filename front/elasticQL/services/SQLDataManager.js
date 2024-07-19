/* 
* ElasticQL SQLDataManager.js module class
 */
import BaseDataManager from '../core/BaseDataManager.js';
export default class SQLDataManager extends BaseDataManager {
  _buildQueryString(params) {
    const { schema, fields, rels, filters } = params;
    const queryParams = new URLSearchParams();
    
    queryParams.append('schema', schema);
    queryParams.append('fields', fields.join(','));
    if (rels) queryParams.append('rels', JSON.stringify(rels));
    
    const whereClause = this._buildWhereClause(filters);
    if (whereClause) queryParams.append('where', whereClause);

    return queryParams.toString();
  }

  _buildMutationObject(params) {
    const { schema, fields, filters } = params;
    let keyBinds = {};
    const values = [];
    let conditions = null;    
    for (const [key, value] of Object.entries(fields)) {
      keyBinds[key] = `:${key}`;
      values.push(this._escapeValue(value));
    }
    const builded = { schema, keyBinds, values }; 
    if (filters) {
      conditions = this._buildWhereClause(filters);
      builded.where = conditions
    }
    return builded;
  }

  _buildWhereClause(filters) {
    if (!filters || Object.keys(filters).length === 0) {
      return '';
    }
    const conditions = [];
    for (const [key, value] of Object.entries(filters)) {
      if (typeof value === 'object') {
        for (const [operator, operand] of Object.entries(value)) {
          switch(operator) {
            case 'gt': conditions.push(`${key} > ${this._escapeValue(operand)}`); break;
            case 'lt': conditions.push(`${key} < ${this._escapeValue(operand)}`); break;
            case 'gte': conditions.push(`${key} >= ${this._escapeValue(operand)}`); break;
            case 'lte': conditions.push(`${key} <= ${this._escapeValue(operand)}`); break;
            case 'eq': conditions.push(`${key} = ${this._escapeValue(operand)}`); break;
            case 'neq': conditions.push(`${key} != ${this._escapeValue(operand)}`); break;
            case 'like': conditions.push(`${key} LIKE ${this._escapeValue(operand)}`); break;
            // Añadir más operadores según sea necesario
          }
        }
      } else {
        conditions.push(`${key} = ${this._escapeValue(value)}`);
      }
    }
    return conditions.join(' AND ');
  }
  _escapeValue(value) {
    // Implementación básica de escape, mejorar según necesidades
    if (typeof value === 'string') {
      return `'${value.replace(/'/g, "''")}'`;
    }
    return value;
  }
}