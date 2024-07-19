/* 
* ElasticQL BaseDataManager JS module class
 */
export default class BaseDataManager {
  constructor(baseUrl) {
    this.baseUrl = baseUrl;
  }
  async query(params) {
    return this._sendRequest('GET', params);
  }
  async create(params) {
    return this._sendRequest('POST', params);
  }
  async update(params) {
    return this._sendRequest('PUT', params);
  }
  async delete(params) {
    return this._sendRequest('DELETE', params);
  }
  _buildQueryString(params) {
    throw new Error("Method '_buildQueryString' must be implemented.");
  }
  _buildMutationObject(params) {
    throw new Error("Method '_buildMutationObject' must be implemented.");
  }
  async _sendRequest(method, params) {
    const url = new URL(this.baseUrl);    
    if (method === 'GET') {
      url.search = this._buildQueryString(params);
    }else if (method === 'POST' || method === 'PUT') {
      params = this._buildMutationObject(params);
    }        
   
    const options = {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      }
    };
    if (method !== 'GET') {
      options.body = JSON.stringify(params);
    }
    return console.log('options', options);
    try {
      const response = await fetch(url, options);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return await response.json();
    } catch (error) {
      console.error("Error en la solicitud:", error);
      throw error;
    }
  }
}