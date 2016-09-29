package com.example.heli.searchengine;

import android.content.Intent;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.View;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

public class Main4Activity extends AppCompatActivity {
    ListView listView1;
    class Documents {
        String url;
        String title;
        String caption;
        String DoC;
        String thumbnail;

        Documents(String url, String title, String caption, String DoC, String thumbnail) {
            this.url = url;
            this.title = title;
            this.caption = caption;
            this.DoC = DoC;
            this.thumbnail = thumbnail;
        }
    }
    private ArrayList<Documents> documents2;
    public static final String REGISTER_URL1 = "http://192.168.227.1:8082/ita_dse/searchByDate1";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main4);
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        Intent i=getIntent();
        String searchstring1=i.getStringExtra("searchstring1");
        TextView textView=(TextView)findViewById(R.id.search);
        textView.setText(searchstring1);

        Log.i("SEARCH STRING", searchstring1);
        // POST parameters
        Map<String, String> params1 = new HashMap<String, String>();
        params1.put("document_author", searchstring1);
        JSONObject jsonObj1 = new JSONObject(params1);
        Log.i("JSON AUTHOR OBJECT", String.valueOf(jsonObj1));

        // Request a json response from the provided URL
        JsonObjectRequest jsonObjRequest = new JsonObjectRequest
                (Request.Method.POST,REGISTER_URL1, jsonObj1, new Response.Listener<JSONObject>()
                {
                    @Override
                    public void onResponse(JSONObject response)
                    {
                        Toast.makeText(getApplicationContext(), "Search on Title result Successfully fetched", Toast.LENGTH_LONG).show();
                        Log.i("AUTHOR RESPONSE MARU", response.toString());
                        showJSON(response.toString());
                    }
                },
                        new Response.ErrorListener()
                        {
                            @Override
                            public void onErrorResponse(VolleyError error)
                            {
                                Log.i("AUTHOR RESPONSE ERROR",error.toString());
                                Toast.makeText(getApplicationContext(), error.toString(), Toast.LENGTH_SHORT).show();
                            }
                        });

        RequestQueue requestQueue = Volley.newRequestQueue(this);
        requestQueue.add(jsonObjRequest);
    }
    private void showJSON(String response){
       // System.out.println(response);
        Log.i("response", response);
        if(response!=null){
            documents2=new ArrayList<>();
            //System.out.println(result);
            try {
                JSONObject jsonRootObject = new JSONObject(response);

                String status=jsonRootObject.getString("status");

                if("success".equals(status)){
                    JSONArray jsonArray = jsonRootObject.optJSONArray("response");

                    for(int i=0; i < jsonArray.length(); i++){
                        JSONObject jsonObject = jsonArray.getJSONObject(i);
                        // System.out.println("User" + i + ": " + jsonObject.getString("user_name"));
                        String title="", caption="";
                        if(jsonObject.getString("title").length()>35) {
                            title = (jsonObject.getString("title")).substring(0, 35);
                        }else{
                            title = jsonObject.getString("title");
                        }

                        if(jsonObject.getString("title").length()>70) {
                            caption = jsonObject.getString("caption").substring(0, 70);
                        }else{
                            caption = jsonObject.getString("caption");
                        }

                        String DoC = jsonObject.getString("DoC").substring(0, jsonObject.getString("DoC").indexOf(' '));
                        documents2.add(new Documents(jsonObject.getString("url"),title,caption,DoC,jsonObject.getString("thumbnail")));


                    }
                    Log.i("DOCUMENT", String.valueOf(documents2));
                    CustomAdapter2 customAdapter2=new CustomAdapter2(Main4Activity.this, documents2);
                    listView1=(ListView)findViewById(R.id.listView2);
                    listView1.setAdapter(customAdapter2);
                }else{
                    String message=jsonRootObject.getString("message");
                }

            } catch (JSONException e) {
                e.printStackTrace();
            }
        }else{
        }
    }
}

